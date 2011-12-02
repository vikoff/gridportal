<?php

class MyproxyConnector {
	
	public $host = null;
	public $port = null;
	public $login = null;
	public $password = null;
	public $lifetime = null;
	
	public $tmpfile = null;
	
	public $connected = FALSE;
	public $errcode = '';
	public $errmsg = '';
	
	
	public function __construct($data){
		
		$this->host = $data['url'];
		$this->port = $data['port'];
		$this->login = $data['login'];
		$this->password = $data['password'];
		$this->lifetime = $data['lifetime'];
	}
	
	public function connect(){
		
		$debug = 0;
		$this->tmpfile = tempnam("/tmp", "x509_mp_");
		
		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		
		$myProxyIsLogged = myproxy_logon(
			$this->host,
			$this->port,
			$this->login,
			$this->password,
			$this->lifetime,
			$this->tmpfile,
			$debug
		);
		
		if ($myProxyIsLogged == 'ok') {
			$this->connected = TRUE;
			return TRUE;
		} else {
			
			$this->errcode = substr($myProxyIsLogged, 0, 3);
			$this->errmsg = substr($myProxyIsLogged, 3);
			
			// сбросить авторизацию
			if ($this->errcode == 104) {
				$user = CurUser::get();
				if(!$user->getField('myproxy_manual_login'))
					$user->resetMyproxyExpireDate();
			}
			return FALSE;
		}
	}
	
	public function getHumanReadableMsg($code){
		
		switch($code) {
			
			case 101: return '';
			case 102: return '';
			case 103: return '';
			case 104: return 'Неверный логин или пароль';
			case 105: return '';
			case 106: return '';
			default: return 'Неизвестный код ошибки: '.$code;
		}
	}
	
}

?>