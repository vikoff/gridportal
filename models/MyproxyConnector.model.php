<?php

class MyproxyConnector {
	
	public $serverId = 0;
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
	
	/**
	 * СОЗДАНИЕ ЭКЗЕМПЛЯРА КОННЕКТОРА НА ОСНОВЕ ДАННЫХ ФОРМЫ
	 * @param array $data - массив с ключами 'myproxy-autologin', 'server',
	 *                      'custom-server', 'custom-server-port', 'login', 'password', 'lifetime'
	 */
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
			$myproxy = array(
				'login' => getVar($data['login']),
				'password' => getVar($data['password']),
				'lifetime' => (int)getVar($data['lifetime']),
			);
			if ($server == 'custom') {
				$customServer = strtolower(getVar($data['custom-server']));
				$customServerPort = getVar($data['custom-server-port']);
				// если кастомный сервер уже сохранен в БД, используем его
				try {
					$myproxyServer = MyproxyServer::loadServer($customServer, $customServerPort)->getAllFields();
					$myproxy['serverId'] = $myproxyServer['id'];
					$myproxy['url'] = $myproxyServer['url'];
					$myproxy['port'] = $myproxyServer['port'];
				}
				// если кастомный сервер отсутствует в БД, используем полученные параметры
				catch (Exception $e) {
					$isCustomServer = TRUE;
					$myproxy['serverId'] =  0;
					$myproxy['url'] = $customServer;
					$myproxy['port'] = $customServerPort;
				}
			} else {
				
				$myproxyServer = MyproxyServer::load((int)$server)->getAllFields();
				$myproxy['serverId'] = $myproxyServer['id'];
				$myproxy['url'] = $myproxyServer['url'];
				$myproxy['port'] = $myproxyServer['port'];
				
			}
		}
		
		return new MyproxyConnector($myproxy, $isCustomServer);
	}
	
	public function __construct($data, $isCustomServer = FALSE){
		
		$this->serverId = $data['serverId'];
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
		$this->serverId = $server->id;
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