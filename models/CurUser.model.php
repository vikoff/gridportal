<?
	
class CurUser extends User{
	
	const TABLE = 'users';
	
	private $_authPrefix = 'Bryi3U6';
	
	private static $_instance = null;
	
	
	// ИНИЦИАЛИЗАЦИЯ ЭКЗЕМПЛЯРА КЛАССА
	public static function init(){
		
		if(!is_null(self::$_instance))
			trigger_error('Объект класса CurUser уже инициализирован', E_USER_ERROR);
		self::$_instance = new CurUser();
	}
	
	// ПОЛУЧЕНИЕ ЭКЗЕМПЛЯРА КЛАССА
	public static function get(){
		
		return self::$_instance;
	}
	
	// КОНСТРУКТОР
	public function __construct(){
		
		if(!$this->isSessionInited())
			$this->initSession();
		
		parent::__construct($this->getAuthData('id'));
	}
	
	// ИНИЦИАЛИЗИРОВАНА ЛИ СЕССИЯ
	public function isSessionInited(){
	
		return isset($_SESSION[$this->_authPrefix.'userAuthData']);
	}
	
	// ИНИЦИАЛИЗАЦИЯ СЕССИИ
	public function initSession(){
	
		$this->setEmptyAuthData();
		if($this->cookieLogin())
			App::reload();
	}
	
	// АВТОРИЗАЦИЯ ПОЛЬЗОВАТЕЛЯ
	public function login($login, $pass, $remember = FALSE){
		
		if($this->isLogged())
			return;
		
		if(!$login || !$pass){
			throw new Exception("Введите имя и пароль.");
		}
		
		$db = db::get();
		
		$login = $db->escape($login);
		$pass = $db->escape($pass);
		
		if($ans = $db->getRow("SELECT id, login, password, level FROM ".self::TABLE." WHERE login = '".$login."' AND password = '".sha1($pass)."'", FALSE)){
		
			// сохранить данные в сессию
			$this->setLoggedAuthData($ans['id'], $ans['level']);
			
			// запомнить пользователя
			if($remember)
				$this->_setLoginCookie($ans['id'], $ans['login'], $ans['password']);
				
		}else{
			throw new Exception('Неверный логин или пароль');
		}
		
	}
	
	// АВТОРИЗАЦИЯ С ПОМОЩЬЮ КУКИ
	public function cookieLogin(){

		if(empty($_COOKIE[$this->_authPrefix.'uid']) || empty($_COOKIE[$this->_authPrefix.'access']))
			return FALSE;
			
		$uid = (int)$_COOKIE[$this->_authPrefix.'uid'];
		
		$ans = db::get()->getRow("SELECT id, login, password, level FROM ".self::TABLE." WHERE id='".$uid."'", 0);
		if(!$ans)
			return false;

		if($_COOKIE[$this->_authPrefix.'access'] == md5('yurijnovikovproject'.$ans['id']."_".$ans['login']."_".$ans['password'])){
		
			$this->setLoggedAuthData($ans['id'], $ans['level']);
			$this->_setLoginCookie($ans['id'], $ans['login'], $ans['password']);
			return TRUE;
			
		}else{
			$this->_setEmptyCookie();
			return FALSE;
		}
	}

	// ВЫХОД ИЗ АККАУНТА
	public function logout(){
		
		UserStatistics::get()->reset();
		$this->setEmptyAuthData();
		$this->_setEmptyCookie();
	}
	
	// УСТАНОВИТЬ КУКИ ДЛЯ ПОСЛЕДУЮЩЕЙ АВТОРИЗАЦИИ
	private function _setLoginCookie($id, $login, $password){
	
		$expire = time() + 60 * 60 * 24 * 365;
		setcookie($this->_authPrefix."uid", $id, $expire);
		setcookie($this->_authPrefix."access", md5('yurijnovikovproject'.$id."_".$login."_".$password), $expire);
	}
	
	// УСТАНОВИТЬ ПУСТЫЕ КУКИ
	private function _setEmptyCookie(){
	
		setcookie($this->_authPrefix."uid", "");
		setcookie($this->_authPrefix."access", "");
	}
	
	// ПРОВЕРКА АВТОРИЗОВАН ЛИ ПОЛЬЗОВАТЕЛЬ
	 public function isLogged(){
		
		return (is_numeric($_SESSION[$this->_authPrefix.'userAuthData']['id']) && in_array($_SESSION[$this->_authPrefix.'userAuthData']['perms'], User::getPermsList()) && $_SESSION[$this->_authPrefix.'userAuthData']['perms'] > PERMS_UNREG)
			? TRUE
			: FALSE;
	}
	
	// УСТАНОВИТЬ ПОЛЗЬОВАТЕЛЬСКИЕ ДАННЫЕ
	 private function setLoggedAuthData($id, $perms){
		
		$_SESSION[$this->_authPrefix.'userAuthData'] = array('id' => $id, 'perms' => $perms);
		
		UserStatistics::get()->saveAuthStatistics($id);
	}
	
	// УСТАНОВИТЬ ПУСТЫЕ ПОЛЬЗОВАТЕЛЬСКИЕ ДАННЫЕ
	 private function setEmptyAuthData(){
	 
		$_SESSION[$this->_authPrefix.'userAuthData'] = array('id' => 0, 'perms' => 0);
	}
	
	public function getAuthData($key = null){
		
		return is_null($key)
			? $_SESSION[$this->_authPrefix.'userAuthData']
			: $_SESSION[$this->_authPrefix.'userAuthData'][$key];
	}
	
}

?>