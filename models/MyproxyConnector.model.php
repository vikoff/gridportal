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
	
	public $isCustomServer = true;
	
	/** СОЗДАНИЕ ЭКЗЕМПЛЯРА КОННЕКТОРА НА ОСНОВЕ ДАННЫХ ФОРМЫ */
	public static function createByConnectForm($data){
		
		$myproxy = array();
		$isCustomServer = false;
		
		if (!empty($data['myproxy-autologin'])) {
			
			$myproxy = CurUser::get()->getMyproxyLoginData();
			$myproxyServer = MyproxyServer::load($myproxy['serverId'])->getAllFields();
			$myproxy['url'] = $myproxyServer['url'];
			$myproxy['port'] = $myproxyServer['port'];
			
			
		} else {
			$server = getVar($data['server']);
			if ($server == 'custom') {
				
				$isCustomServer = TRUE;
				$myproxy = array(
					'url' => getVar($data['custom-server']),
					'port' => getVar($data['custom-server-port']),
					'login' => getVar($data['login']),
					'password' => getVar($data['password']),
					'lifetime' => (int)getVar($data['lifetime']),
				);
			} else {
				
				$myproxyServer = MyproxyServer::load((int)$server)->getAllFields();
				$myproxy = array(
					'url' => $myproxyServer['url'],
					'port' => $myproxyServer['port'],
					'login' => getVar($data['user']['name']),
					'password' => getVar($data['user']['pass']),
					'lifetime' => (int)getVar($data['lifetime']),
				);
				
			}
		}
		
		return new MyproxyConnector($myproxy, $isCustomServer);
	}
	
	public function __construct($data, $isCustomServer = FALSE){
		
		$this->host = $data['url'];
		$this->port = $data['port'];
		$this->login = $data['login'];
		$this->password = $data['password'];
		$this->lifetime = $data['lifetime'];
		
		$this->isCustomServer = $isCustomServer;
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
			if ($this->isCustomServer)
				$this->saveCustomServer();
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
	
	public function saveCustomServer(){
		
		$server = MyproxyServer::create();
		$server->userDefined(USER_AUTH_ID);
		$server->save(array(
			'name' => $this->host,
			'url' => $this->host,
			'port' => $this->port
		));
		CurUser::get()->saveMyproxyAuthData(array(
			'login' => $this->login,
			'password' => $this->password,
			'server_id' => $server->id,
			'lifetime' => $this->lifetime,
		));
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