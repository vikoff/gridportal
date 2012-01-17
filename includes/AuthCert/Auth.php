<?php
// requires: php-soap

interface iAuth {
	// Status reports
	public function getDN();
	public function getName();
	public function getAuthStatus();
	// Configuration interfaces
	public function addAuthServer($url);
	public function checkAuth();
}

class iAuthLang {
	public $auth_check_status = array ();
}

class CertAuth_Lang_en extends iAuthLang {
	public $auth_check_status = array (
		"0"	=> "Ok",
		"1"	=> "Authentication check failed",
		"2"	=> "You need to use HTTPS to authenticate",
		"3"	=> "Client certificate does not provided",
		"4"	=> "Provided certificate is not valid",
		"5"	=> "Connetion to the VOMS server faileld",
		"6"	=> "VOMS Membership is not confirmed",
	);
}

class CertAuth implements iAuth {
	private $status = 1;
	private $dn = 0;
	private $cn = 0;
	private $vo;
	private $vomses = array ();
	private $server_info;
	private $l;

	// class constructor
	function __construct() {
		$this->l = new CertAuth_Lang_en();
		$this->server_info['HTTPS']		= isset($_SERVER['HTTPS'])		? $_SERVER['HTTPS']		:0;
		$this->server_info['SSL_CLIENT_S_DN']	= isset($_SERVER['SSL_CLIENT_S_DN'])	? $_SERVER['SSL_CLIENT_S_DN']	:0;
		$this->server_info['SSL_CLIENT_I_DN']	= isset($_SERVER['SSL_CLIENT_I_DN'])	? $_SERVER['SSL_CLIENT_I_DN']	:0;
		$this->server_info['SSL_CLIENT_VERIFY']	= isset($_SERVER['SSL_CLIENT_VERIFY'])	? $_SERVER['SSL_CLIENT_VERIFY']	:0;
	}

	// return common name for specified distinguished name
	private function CNfromDN ( $dn ) {
		$cn = "";
		if ( preg_match("!/CN=!", $dn) ) {
			$dot_count = $space_count = 0;
			foreach ( preg_split("!/CN=!",$dn) as $dn_field ) {
				if ( ! preg_match("/=/", $dn_field) ) {
					// Check if CN have spaces
					$s = preg_match("/\s/", $dn_field);
					if ( $s >= $space_count ) {
						$cn = $dn_field;
						$space_count = $s;
					}
					// Check if CN have dots
					$d = preg_match("/\./", $dn_field);
					if ( $d >= $dot_count ) {
						$dot_cn = $dn_field;
						$dot_count = $d;
					}
				}
			}
			// If we have record with spaces (more spaces is preferable) - use it as CN
			// If we have no records with spaces, but have records with dots (more dots is preferable) - use it as CN
			// Otherwise just use lact CN record
			if ( ($space_count === 0) && ($dot_count > 0) ) $cn = $dot_cn;
			$cn = preg_replace("/^CN=/","",$cn);
		}
		return $cn;
	}

	// check VOMS membership
	private function checkVOMS () {
		if ( empty($this->vomses) ) {
			$this->status = 0;
			return 0;
		}
		foreach ( $this->vomses as $voms_url ) {
			$gridmapusers = 0;
			try {
				$soap_client = new SoapClient($voms_url."?wsdl", array(
					"local_cert" => SERVICE_CERTIFICATE
				));
				//var_dump($soap_client->__getFunctions());
				$gridmapusers = $soap_client->getGridmapUsers();
			} catch (Exception $e) { 
				$this->status = 5;
			}
			if ( $gridmapusers ) break;
		}
		if ( $gridmapusers ) {
			if ( in_array($this->dn, $gridmapusers) ) {
				$this->status = 0;
			} else {
				$this->status = 6;
			}
		} else {
			$this->status = 5;
		}
		return $this->status;
	}


	// return user DN
	public function getDN() {
		return $this->dn;
	}

	// return user CN
	public function getName() {
		if ( $this->cn ) return $this->cn;
		$this->cn = $this->CNfromDN($this->dn);
		return $this->cn;
	}

	// return authorization status
	public function getAuthStatus() {
		return $this->status;
	}
	
	// return authorization status name
	public function getAuthStatusName() {
		return $this->l->auth_check_status[$this->status];
	}
	
	// add voms server for auth check
	public function addAuthServer ($url) {
		#$gridmap_url = "http://" . $url . "/services/VOMSCompatibility?method=getGridmapUsers";
		$gridmap_url = "https://" . $url . "/services/VOMSCompatibility";
		$this->vomses[] = $gridmap_url;
	}

	// check authorization
	public function checkAuth () {
		// check HTTPS first
		// print_r($this->server_info);
		if (! $this->server_info['HTTPS'] ) {
			$this->status = 2;
		// check certifate is valid
		} else if ( $this->server_info['SSL_CLIENT_VERIFY'] !== 'SUCCESS' ) {
			if ( $this->server_info['SSL_CLIENT_VERIFY'] !== 'NONE' ) $this->status = 3;
			else $this->status = 4;
		// check VOMS membership
		} else {
			$this->dn = $this->server_info['SSL_CLIENT_S_DN'];
			$this->checkVOMS(); 
		}
		return $this->status;
	}
}

?>
