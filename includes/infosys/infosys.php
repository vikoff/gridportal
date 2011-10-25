<?php


interface iInfosystem {
	// return last error text
	public function get_LastError ();
	// set infosystem query timeouts specified in hash
	// following timeouts handled: bind, search, query, allqueries
	public function set_Timeouts ( $timeout );
	// add server to query specified by params hash
	public function add_Server ( $params );
	// return attributes for entries match filter (if list of attributes not specified return all)
	public function query_Infosys ( $filter, $attrs );
}
	
class iInfosystem_Lang {
	public $infosys_bad_server_params;
	public $infosys_could_not_connect;
	public $infosys_could_not_bind;
	public $infosys_could_not_query;
}

class BDIIQuery_Lang_en extends iInfosystem_Lang {
	public $infosys_bad_server_params = "Bad BDII server parameters specified.";
	public $infosys_could_not_connect = "Could not connect to %s server.";
	public $infosys_could_not_bind = "Could not bind to %s server.";
	public $infosys_could_not_query = "Could not query %s server.";
}


class BDIIQuery implements iInfosystem {
	private $servers = array ();
	private $l;
	private $error_text;
	private $timeouts = array ( 
		"bind"		=> 10,
		"search"	=> 30,
		"query"		=> 60,
		"allqueries"	=> 120
	);

	// params ( "server" => "hostname", "port" => "port value", "basedn" => "dn value" )
	function __construct ( $params = array () ) {
		$this->l = new BDIIQuery_Lang_en();
		if ( ! empty( $params ) ) {
			if ( ! isset ($params['server']) ) die ( $this->l->infosys_bad_server_params );
			if ( ! isset ($params['basedn']) ) die ( $this->l->infosys_bad_server_params );
			if ( ! isset ($params['port']) ) $params['port'] = 2170;
			$lconnect = ldap_connect($params['server'],$params['port']) 
				or die ( sprintf($this->l->infosys_could_not_connect, $params['server']) );
			ldap_close($lconnect);
			$this->servers[] = $params;
		}
	}

	public function get_LastError () {
		return $this->error_text;
	}

	public function set_Timeouts ( $timeouts_v) {
		foreach ( array('bind','search','query','allqueries') as $tt ) {
			if ( isset ( $timeouts_v[$tt] ) ) 
				if ( is_int($timeouts_v[$tt]))
					$this->timeouts[$tt] = $timeouts_v[$tt];
		}
	}

	public function add_Server ( $params ) {
		if ( ! empty( $params ) ) {
			if ( ! isset ($params['server']) ) {
				$this->error_text = $this->l->infosys_bad_server_params;
				return false;
			}
			if ( ! isset ($params['basedn']) ) {
				$this->error_text = $this->l->infosys_bad_server_params;
				return false;
			}
			if ( ! isset ($params['port']) ) $params['port'] = 2170;
			$lconnect = ldap_connect($params['server'],$params['port']);
			if ( ! $lconnect ) {
				$this->error_text = $this->l->infosys_could_not_connect;
				return false;
			} else ldap_close($lconnect);
			$this->servers[] = $params;
		} else return false;
		return true;
	}

	// return binded connection handler for ldap connection, false on fail
	private function bind_Server ( $params ) {
		$timelimit = time() + $this->timeouts['bind'];
		$lconnect = ldap_connect($params['server'],$params['port']);
		$bind = false;
		while ( (!$bind) && ( time() < $timelimit ) )
		{
			$bind = @ldap_bind($lconnect);
			if(!$bind) { sleep(1); }
		}
		if (!$bind) {
			$this->error_text = sprintf($this->l->infosys_could_not_bind, $params['server']);
			ldap_close($lconnect);
			return false;
		}
		return $lconnect;
	}

	public function query_Infosys ($filter, $attrs ) {
		$a_timelimit = time() + $this->timeouts['allqueries'];
		$q_result = array ();
		$qerror_text = "";
		$qerror_flag = false;
		foreach ( $this->servers as $ls ) {
			// connect to server
			$lconnect = $this->bind_Server($ls);
			if ( ! $lconnect ) return false;
			// set timeout limits
			$timelimit = time() + $this->timeouts['query'];
			$ldp_errno = 0x01;
			$sarray = array ();
			while ( ( $ldp_errno != 0x00 ) && ( time() < $timelimit ) && ( time() < $a_timelimit ) ) {
				$sarray = @ldap_search($lconnect, $ls['basedn'], $filter, $attrs, 0, 0, $this->timeouts['search']);
				$ldp_errno = ldap_errno($lconnect);
				if ( $ldp_errno != 0x00 ) { sleep(1); }
			}
			if ( $ldp_errno != 0x00 ) {
				ldap_close($lconnect);
				$errnmsg = ldap_error($lconnect);
				$qerror_text .= sprintf($this->l->infosys_could_not_query . ": %s \n", $ls['server'], $errnmsg);
				$qerror_flag = true;
			} else {
				$entrys = ldap_get_entries($lconnect, $sarray);
				if(is_array($entrys))
					$q_result = array_merge($q_result, $entrys);
				ldap_close($lconnect);
			}
		}
		if ( $qerror_flag ) $this->error_text = $qerror_text;
		return $q_result;
	}
}

class BDIIQuery_ARCJobs extends BDIIQuery {
	public function query_ARCJobs ($jobids = array (), $attrs) {
		// construct filter to select job ids
		$filter = "(|";
		foreach ( $jobids as $jobid ) {
			$filter .= "(nordugrid-job-globalid=". $jobid .")";
		}
		$filter .= ")";
		// implicitly add nordugrid-job-globalid to required attrs
		if ( ! in_array("nordugrid-job-globalid", $attrs))
			$attrs = array_merge($attrs, array("nordugrid-job-globalid"));
		// do query
		$result = $this->query_Infosys ($filter, $attrs);
		if ( ! $result ) return false;
		// format output
		$fresult = array();
		for ($i = 0; $i < $result["count"]; $i++) {
			$jobid = $result[$i]["nordugrid-job-globalid"][0];
			$arrts_val = array ();
			foreach ( $attrs as $idx => $attr_name ) 
				if ( $attr_name === "nordugrid-job-globalid" ) unset($attrs[$idx]);
			foreach ( $attrs as $attr_name ) {
				$arrts_val[$attr_name] = $result[$i][$attr_name][0];
			}
			$fresult[$jobid] = $arrts_val;
		}
		return $fresult;
	}

	public function query_VOProcs ($vo) {
		// query totalcpus for clusters that support specified VO
		$filter = "(&(nordugrid-cluster-name=*)(nordugrid-cluster-acl=VO:". $vo ."))";
		$attrs = array("nordugrid-cluster-totalcpus");
		$result = $this->query_Infosys ($filter, $attrs);
		if ( ! $result ) return false;
		// count processors
		$procs = 0;
		for ($i = 0; $i < $result["count"]; $i++) {
			$procs += $result[$i]["nordugrid-cluster-totalcpus"][0];
		}
		return $procs;
	}
}

// ---- test functionality

/* $bdii = array ( 
	"server" => "bdii.grid.org.ua",
	"port" => 2170,
	"basedn" => "Mds-Vo-name=local,o=grid"
);

echo "<pre>";

// подключение к ldap
$query = new BDIIQuery_ARCJobs($bdii);

printf("ukraine: %d procs \n", $query->query_VOProcs("ukraine"));
printf("networkdynamics: %d procs \n", $query->query_VOProcs("networkdynamics"));
printf("moldyngrid: %d procs \n", $query->query_VOProcs("moldyngrid"));

//$jobs = array ( '*' );
$jobs = array ('gsiftp://nordu.hpcc.ntu-kpi.kiev.ua:2811/jobs/2982012923356881334399975', 'gsiftp://arc.hpcc.kpi.ua:2811/jobs/259481292771499878579415','gsiftp://uagrid.org.ua:2811/jobs/157001293026447328673414');
$attrs = array ( 'nordugrid-job-status', 'nordugrid-job-jobname', 'nordugrid-job-globalowner');
print_r($query->query_ARCJobs($jobs,$attrs));

echo "</pre>";
*/

?>