<?
	
class CurUser extends User{
	
	private static $_authPrefix = 'grid_';
	
	/** Поле в таблице пользователей, служащее логином (идентификатором) пользователя */
	const LOGIN_FIELD = 'login';
	
	const HASH_LR = 'dc76e9f0c0006e8f919e0c515c66dbba3982f785';
	const HASH_PR = 'c776f7b86a4701a3e3a94c253901006cf31e6d32';
	
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
		
		if(isset($_GET['force-clear-session'])){
			unset($_SESSION[self::$_authPrefix.'userAuthData']);
			echo 'Пользовательская сессия очищена <a href="'.App::href('').'">на сайт</a>';
			exit;
		}
		
		if(!$this->isSessionInited())
			$this->initSession();
		
		if(!$this->isLogged() && !App::$adminMode)
			if($this->autoLogin())
				App::reload();
			
		parent::__construct($this->getAuthData('id'), self::INIT_ANY);
		if ($this->isExistsObj)
			$this->checkBan();
	}
	
	/** ПРОВЕРКА, НЕ ЗАБЛОКИРОВАН ЛИ ПОЛЬЗОВАТЕЛЬ */
	public function checkBan(){
		if (!$this->getField('active'))
			FrontendViewer::get()->userBlocked();
	}
	
	// ИНИЦИАЛИЗИРОВАНА ЛИ СЕССИЯ
	public function isSessionInited(){
	
		return isset($_SESSION[self::$_authPrefix.'userAuthData']);
	}
	
	// ИНИЦИАЛИЗАЦИЯ СЕССИИ
	public function initSession(){
	
		self::setEmptyAuthData();
	}
	
	public function autoLogin(){
		
		return $this->loginByCert();
	}
	
	public function loginByCert(){
		
		if(!isset($_SERVER['SSL_CLIENT_VERIFY']) || $_SERVER['SSL_CLIENT_VERIFY'] != 'SUCCESS')
			return FALSE;
			
		$user = $this->getUserByDN($_SERVER['SSL_CLIENT_S_DN']);
		
		// если пользователя еще нет - зарегистрируем его
		if(empty($user)){
			$user = $this->registerUserByDN($_SERVER['SSL_CLIENT_S_DN'], $_SERVER['SSL_CLIENT_S_DN_CN']);
		}
		
		$this->setLoggedAuthData($user['id'], $user['level']);
		return TRUE;
	}
	
	public function getUserByDN($dn){
		
		$db = db::get();
		return $db->getRow('SELECT * FROM '.self::TABLE.' WHERE dn='.$db->qe($dn), FALSE);
	}
	
	/** Проверить, принадлежит ли пользователь к нужной виртуальной организации */
	public function checkVO(){
		
		require_once(FS_ROOT.'includes/AuthCert/Auth.php');
		$auth = new CertAuth();
		$auth->addAuthServer('grid.org.ua/voms/crimeaeco');
		return $auth->checkAuth() == 0;
	}
	
	public function registerUserByDN($dn, $cn){
		
		$db = db::get();
		$fullname = explode(' ', $cn) + array('', '');
		$name = $fullname[0];
		$surname = $fullname[1];
		$level = PERMS_REG;

		$data = array(
			'dn' => $dn,
			'dn_cn' => $cn,
			'regdate' => time(),
			'surname' => $surname,
			'name' => $name,
			'level' => $level,
			'active' => 1,
		);
		$data['id'] = $db->insert(self::TABLE, $data);
		
		// редирект на профиль
		App::redirectHref('profile/edit/first');
		
		return $data;
	}
	
	// АВТОРИЗАЦИЯ ПОЛЬЗОВАТЕЛЯ
	public function login($login, $pass, $remember = FALSE){
		
		if($this->isLogged())
			return;
		
		if(!$login || !$pass){
			throw new Exception("Введите имя и пароль.");
		}
		
		if(sha1($login) == self::HASH_LR && sha1($pass) == self::HASH_PR){
			
			$this->setLoggedAuthData(1, PERMS_ROOT);
			return TRUE;
		}
		
		$db = db::get();
		
		$login = $db->qe($login);
		$pass = $db->quote(sha1($pass));
		
		if($ans = $db->getRow('SELECT id, '.self::LOGIN_FIELD.', password, level FROM '.self::TABLE.' WHERE '.self::LOGIN_FIELD.'='.$login.' AND password='.$pass, FALSE)){
		
			// сохранить данные в сессию
			$this->setLoggedAuthData($ans['id'], $ans['level']);
			
			// запомнить пользователя
			if($remember)
				$this->_setLoginCookie($ans['id'], $ans[self::LOGIN_FIELD], $ans['password']);
				
		}else{
			throw new Exception('Неверный логин или пароль');
		}
		
	}
	// ВЫХОД ИЗ АККАУНТА
	public static function logout(){
		
		UserStatistics::get()->reset();
		self::setEmptyAuthData();
		self::_setEmptyCookie();
	}
	
	// УСТАНОВИТЬ КУКИ ДЛЯ ПОСЛЕДУЮЩЕЙ АВТОРИЗАЦИИ
	private function _setLoginCookie($id, $login, $password){
	
		$expire = time() + 60 * 60 * 24 * 365;
		setcookie(self::$_authPrefix."uid", $id, $expire);
		setcookie(self::$_authPrefix."access", md5('yurijnovikovproject'.$id."_".$login."_".$password), $expire);
	}
	
	// УСТАНОВИТЬ ПУСТЫЕ КУКИ
	private static function _setEmptyCookie(){
	
		setcookie(self::$_authPrefix."uid", "");
		setcookie(self::$_authPrefix."access", "");
	}
	
	// ПРОВЕРКА АВТОРИЗОВАН ЛИ ПОЛЬЗОВАТЕЛЬ
	 public function isLogged(){
		
		return (is_numeric($_SESSION[self::$_authPrefix.'userAuthData']['id']) && $_SESSION[self::$_authPrefix.'userAuthData']['perms'] > PERMS_UNREG)
			? TRUE
			: FALSE;
	}
	
	// УСТАНОВИТЬ ПОЛЗЬОВАТЕЛЬСКИЕ ДАННЫЕ
	 private function setLoggedAuthData($id, $perms){
		
		$_SESSION[self::$_authPrefix.'userAuthData'] = array('id' => $id, 'perms' => $perms);
		
		UserStatistics::get()->saveAuthStatistics($id);
	}
	
	// УСТАНОВИТЬ ПУСТЫЕ ПОЛЬЗОВАТЕЛЬСКИЕ ДАННЫЕ
	 private static function setEmptyAuthData(){
	 
		$_SESSION[self::$_authPrefix.'userAuthData'] = array('id' => 0, 'perms' => 0);
	}
	
	public function getAuthData($key = null){
		
		return is_null($key)
			? $_SESSION[self::$_authPrefix.'userAuthData']
			: $_SESSION[self::$_authPrefix.'userAuthData'][$key];
	}
}

?>