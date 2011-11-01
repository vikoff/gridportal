<?

class User extends GenericObject{
	
	// пол
	const GENDER_FEMALE 	= 'f';
	const GENDER_MALE		= 'm';
	
	const TABLE = 'users';
	
	const NOT_FOUND_MESSAGE = 'Пользователь не найден';

	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new User(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new User($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new User($id, self::INIT_EXISTS_FORCE, $fieldvalues);
	}
	
	// КОНСТРУКТОР
	public function __construct($id){
		
		parent::__construct($id, self::INIT_ANY);
	}
	
	/** СЛУЖЕБНЫЙ МЕТОД (получение констант из родителя) */
	public function getConst($name){
		return constant(__CLASS__.'::'.$name);
	}

	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(array(
				'required' => array('login', 'email', 'password', 'password_confirm', 'surname', 'name', 'patronymic', 'sex', 'birthdate', 'country', 'region', 'settlement', 'city'),
				'strip' => '*',
			),
			array(
				'login' => array('length' => array('max' => '255')),
				'email' => array('length' => array('max' => '100'), 'email' => true),
				'password' => array('length' => array('min' => '5', 'max' => '100'), 'password' => array('hash' => 'sha1')),
				'password_confirm' => array('compare' => 'password', 'unsetAfter' => TRUE),
				'surname' => array('length' => array('max' => '255')),
				'name' => array('length' => array('max' => '255')),
				'patronymic' => array('length' => array('max' => '255')),
				'sex' => array('length' => array('max' => '10')),
				'birthdate' => array('dbDate' => TRUE),
				'country' => array('length' => array('max' => '255')),
				'city' => array('length' => array('max' => '255')),
				'captcha' => array('captcha' => isset($_SESSION['captcha']) ? $_SESSION['captcha'] : ''),
			));
			$this->validator->setFieldTitles(array(
				'login' => 'Логин',
				'email' => 'email-адрес',
				'password' => 'пароль',
				'password_confirm' => 'подтверждение пароля',
				'surname' => 'фамилия',
				'name' => 'имя',
				'patronymic' => 'отчество',
				'sex' => 'пол',
				'birthdate' => 'дата рождения',
				'country' => 'страна',
				'city' => 'город',
			));
		}
		
		// применение специальных правил для редактирования или добавления объекта
		if($this->isExistsObj){
			$this->validator->delElement(array('login', 'password', 'password_confirm', 'captcha'));
		}
		
		return $this->validator;
	}
	
	public function validateProfile(){
		
		$validator = new Validator();
		$validator->rules(array(
			'strip' => '*',
		),
		array(
			'name' => array('length' => array('max' => '255')),
			'login' => array('length' => array('max' => '255')),
			'email' => array('length' => array('max' => '100'), 'email' => true),
			'password' => array('length' => array('min' => '5', 'max' => '100'), 'password' => array('hash' => 'sha1')),
			'password_confirm' => array('compare' => 'password', 'unsetAfter' => TRUE),
			'surname' => array('length' => array('max' => '255')),
			'name' => array('length' => array('max' => '255')),
			'patronymic' => array('length' => array('max' => '255')),
			'sex' => array('length' => array('max' => '10')),
			'birthdate' => array('dbDate' => TRUE),
			'country' => array('length' => array('max' => '255')),
			'city' => array('length' => array('max' => '255')),
		));
		$validator->setFieldTitles(array(
			'login' => 'Логин',
			'email' => 'email-адрес',
			'password' => 'пароль',
			'password_confirm' => 'подтверждение пароля',
			'surname' => 'фамилия',
			'name' => 'имя',
			'patronymic' => 'отчество',
			'sex' => 'пол',
			'birthdate' => 'дата рождения',
			'country' => 'страна',
			'city' => 'город',
		));
		
		return $validator;
	}
	
	/** ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ */
	public function beforeDisplay($data){
		
		switch(getVar($data['sex'])){
			case 'man': $data['sex'] = 'М'; break;
			case 'woman': $data['sex'] = 'Ж'; break;
			default: $data['sex'] = ' - ';
		}
		
		$level = getVar($data['level']);
		$data['level'] = in_array($level, self::getPermsList()) ? self::getPermName($level) : '<span class="red">Некорректное значение: "'.$level.'"</span>';
		$data['fio'] = $this->getName();
		$data['birthdate'] = YDate::loadDbDate(getVar($data['birthdate']))->getStrDate();
		$data['regdate'] = YDate::loadTimestamp(getVar($data['regdate']))->getStrDateShortTime();
		$data['profile'] = !empty($data['profile']) ? unserialize($data['profile']) : array();
		$data['myproxy_password'] = !empty($data['myproxy_password']) ? base64_decode($data['myproxy_password']) : '';
		
		// echo '<pre>'; print_r($data) ;die;
		return $data;
	}
	
	public function preValidation(&$data){
		
		$data['birthdate'] = YDate::loadArray($data['birth'])->getDbDate();
	}
	
	public function postValidation(&$data){
		
		if($this->isNewObj && self::isEmailInUse($data['email'])){
			$this->setError('Данные email-адрес уже используется, возможно Вам следует воспользатся функцией <a href="'.App::href('profile/forget-password').'">восстановления учетной записи</a>');
			return FALSE;
		}
		if($this->isNewObj){
			$data['level'] = PERMS_REG;
			$data['active'] = '1';
			$data['regdate'] = time();
		}
	}
	
	public function afterSave($data){
		
	}
	
	/** ПОЛУЧИТЬ ИМЯ ПОЛЬЗОВАТЕЛЯ */
	public function getName($name = null){
		
		$outputArr = array();
		
		if(is_null($name))
			$name = 'fio';
		
		for($i = 0; $i < strlen($name); $i++){
			if($name{$i} == 'f')
				$outputArr[] = $this->getField('surname');
			elseif($name{$i} == 'i')
				$outputArr[] = $this->getField('name');
			elseif($name{$i} == 'o')
				$outputArr[] = '';
			else
				trigger_error('Неизвестный код имени: "'.$name{$i}.'"', E_USER_ERROR);
		}
		$output = trim(implode(' ', $outputArr));
		return strlen($output) ? $output : $this->getField('login');
	}
	
	/** УСТАНОВИТЬ НОВЫЙ ПАРОЛЬ */
	public function setNewPassword($oldPassword, $newPassword, $newPasswordConfirm){
		
		if($newPassword != $newPasswordConfirm){
			$this->setError('Пароль и подтверждение не совпадают');
			return FALSE;
		}
		
		if(sha1($oldPassword) != db::get()->getOne('SELECT password FROM '.self::TABLE.' WHERE id='.$this->getField('id'), '')){
			$this->setError('Вы неверно ввели старый пароль');
			return FALSE;
		}
		
		db::get()->update(self::TABLE, array('password' => sha1($newPassword)), 'id='.$this->getField('id'));
		return TRUE;
	}
	
	/** УСТАНОВИТЬ НОВЫЙ УРОВЕНЬ ПРАВ */
	public function setPerms($newPerms){
	
		if(!in_array($newPerms, self::getPermsList())){
			$this->setError('Неверный идентификатор пользовательских прав "'.$newPerms.'"');
			return FALSE;
		}
		
		if($this->getField('level') > USER_AUTH_PERMS){
			$this->setError('Невозможно изменять права пользователю, с правами выше текущего.');
			return FALSE;
		}
		
		if($newPerms == 0 || $newPerms > USER_AUTH_PERMS){
			$this->setError('Невозможно присвоить пользователю уровень прав "'.self::getPermName($newPerms).'"');
			return FALSE;
		}
		
		$this
			->setField('level', $newPerms)
			->_save();
		
		return TRUE;
	}
	
	public function saveProfile($data){
		
		$validator = new Validator();
		$validator->rules(array(), array(
			// 'name'        => array('length' => array('max' => '255')),
			'email'       => array('email' => true),
			'phone'       => array('length' => array('max' => '255')),
			'messager'    => array('length' => array('max' => '255')),
		));
		$validator->setFieldTitles(array(
			'email'       => 'email',
			'phone'       => 'Телефон',
			'messager'    => 'Мессенджер',
		));
		
		$profileData = $validator->validate($data);
		
		
		if($validator->hasError()){
			$this->setError($validator->getError());
			return FALSE;
		}
		
		// сохранение профиля
		$this->setField('profile', serialize($profileData));
		$this->_save();
		$this->fieldValuesForDisplay = $this->beforeDisplay($this->dbFieldValues);
		
		$db = db::get();
		
		// сохранение проектов, выбранных пользователем
		$db->delete('user_allowed_projects', 'uid='.$this->id);
		if(!empty($data['projects']) && is_array($data['projects'])){
			foreach($data['projects'] as $p){
				$pid = (int)$p;
				if($pid)
					$db->insert('user_allowed_projects', array('uid' => $this->id, 'project_id' => $pid));
			}
		}
		
		// сохранение программ, выбранных пользователем
		$db->delete('user_allowed_software', 'uid='.$this->id);
		if(!empty($data['software']) && is_array($data['software'])){
			foreach($data['software'] as $s){
				$sid = (int)$s;
				if($sid)
					$db->insert('user_allowed_software', array('uid' => $this->id, 'software_id' => $sid));
			}
		}
		
		return TRUE;
	}
	
	public function checkVoms($vomsIds){
		
		// проверка id voms
		foreach($vomsIds as $k => $v){
			$v = (int)$v;
			if($v)
				$vomsIds[$k] = $v;
			else
				unset($vomsIds[$k]);
		}
		
		if(empty($vomsIds)){
			$this->setError('Виртуальные организации не выбраны');
			return;
		}
		
		$db = db::get();
		$voms = $db->getAll('SELECT * FROM voms WHERE id IN('.implode(',', $vomsIds).')');
		
		if(empty($voms)){
			$this->setError('Виртуальные организации не найдены');
			return;
		}
		
		require_once(FS_ROOT.'includes/AuthCert/Auth.php');

		$oldAcceptedVOMS = array_flip($db->getCol('SELECT voms_id FROM user_accepted_voms WHERE uid='.$db->qe($this->id), array()));
		$numAcceptedVOMS = 0;
		
		// цикл по всем VO
		foreach($voms as $v){
		
			$auth = new CertAuth();
			$auth->addAuthServer($v['url']); // 'grid.org.ua/voms/crimeaeco'
			
			// если человек состоит в VO
			if($auth->checkAuth() == 0){
				
				$numAcceptedVOMS++;
				
				// если раньше не состоял - сохраним
				if(!isset($oldAcceptedVOMS[$v['id']]))
					$db->insert('user_accepted_voms', array('uid' => $this->id, 'voms_id' => $v['id']));
			}
			// если человек не состоит в  VO
			else{
				
				// если раньше состоял - удалим
				if(isset($oldAcceptedVOMS[$v['id']]))
					$db->delete('user_accepted_voms', 'uid='.$this->id.' AND voms_id='.$v['id']);
			}
		}
		
		return $numAcceptedVOMS;
	}
	
	public function setDefaultVoms($projectVoms){
	
		$db = db::get();
		foreach($projectVoms as $p => $vo){
			$p = (int)$p;
			$vo = (int)$vo;
			if($db->getOne('SELECT COUNT(1) FROM project_allowed_voms WHERE project_id='.$p.' AND voms_id='.$vo))
				$db->update('user_allowed_projects', array('default_vo' => $vo), 'uid='.$this->id.' AND project_id='.$p);
		}
		return TRUE;
	}
	
	public function setManualMyproxyLogon($boolManual){
		
		$this->setField('myproxy_manual_login', (bool)$boolManual);
		$this->_save();
	}
	
	public function checkCert($login, $password, $server_id, $ttl){
		
		//проверка существует ли сервер с переданным id
		try{
			$serverData = MyproxyServer::load($server_id)->getAllFieldsPrepared();
		}catch(Exception $e){
			$this->setError('Сервер myproxy не найден');
			return;
		}
		
		$myproxySuccess = $this->myproxyLogon($serverData['url'], $serverData['port'], $login, $password, $ttl);
		
		if(!$myproxySuccess){
			$this->setError('Авторизация myproxy не пройдена');
			return FALSE;
		}
		
		$this->setFields(array(
			'myproxy_manual_login' => FALSE,
			'myproxy_no_password' => FALSE,
			'myproxy_login' => $login,
			'myproxy_password' => base64_encode($password),
			'myproxy_server_id' => $server_id,
			'myproxy_expire_date' => time() + (int)$ttl,
		));
		$this->_save();
		
		return TRUE;
	}
	
	/** ЗАНЯТ ЛИ EMAIL */
	static public function isEmailInUse($email){
	
		return (bool)db::get()->getOne('SELECT COUNT(1) FROM '.self::TABLE.' WHERE email='.db::get()->qe($email), FALSE);
	}
	
	/** ЗАНЯТ ЛИ ЛОГИН */
	static public function isLoginInUse($login){
		
		return (bool)db::get()->getOne('SELECT COUNT(1) FROM '.self::TABLE.' WHERE login='.db::get()->qe($login), FALSE);
	}
	
	/** ПРОВЕРИТЬ, ИМЕЕТ ЛИ ПОЛЬЗОВАТЕЛЬ УКАЗАННЫЕ ПРАВА */
	static public function hasPerm($perm){

		return USER_AUTH_PERMS >= $perm;
	}

	/** ПОЛУЧИТЬ СПИСОК ВОЗМОЖНЫХ ПРАВ ПОЛЬЗОВАТЕЛЕЙ */
	static public function getPermsList(){
		return array(PERMS_UNREG, PERMS_ALIEN, PERMS_REG, PERMS_MODERATOR, PERMS_ADMIN, PERMS_SUPERADMIN, PERMS_ROOT);
	}
	
	/** ПОЛУЧИТЬ ТЕКСТОВОЕ НАЗВАНИЕ ПРАВ ПОЛЬЗОВАТЕЛЯ */
	static public function getPermName($perm){
		switch($perm){
			case PERMS_UNREG:
				return 'Гость';
			case PERMS_ALIEN:
				return 'Пользователь';
			case PERMS_REG:
				return 'Пользователь';
			case PERMS_MODERATOR:
				return 'Модератор';
			case PERMS_ADMIN:
				return 'Администратор';
			case PERMS_SUPERADMIN:
				return 'Суперадминистратор';
			case PERMS_ROOT:
				return 'ROOT';
			default:
				trigger_error('Неверная группа пользователей: '.$perm, E_USER_ERROR);
		}
	}
	
	/** ТЕКСТОВОЕ ЗНАЧЕНИЕ ПАРАМЕТРА "ПОЛ ПОЛЬЗОВАТЕЛЯ" */
	static public function getGenderString($gender){
		if($gender == self::GENDER_FEMALE)
			return 'женщина';
		elseif($gender == self::GENDER_MALE)
			return 'мужчина';
		else
			return 'не указан';
	}
	
	/** ПОЛУЧИТЬ СПИСОК ПРОЕКТОВ, В КОТОРЫХ УЧАСТВУЕТ ПОЛЬЗОВАТЕЛЬ */
	public function getAllowedProjects(){
		
		return db::get()->getAllIndexed('SELECT p.* FROM '.Project::TABLE.' p JOIN user_allowed_projects ap ON ap.project_id=p.id WHERE ap.uid='.$this->id, 'id');
	}
	
	/** ПОЛУЧИТЬ СПИСОК ВО, В КОТОРЫХ СОСТОИТ ПОЛЬЗОВАТЕЛЬ */
	public function getAllowedVoms(){
		
		return db::get()->getAllIndexed('SELECT v.* FROM '.Voms::TABLE.' v JOIN user_accepted_voms uv ON uv.voms_id=v.id WHERE uv.uid='.$this->id, 'id');
	}
	
	/**ПОЛУЧИТЬ СПИСОК ВО, КОТОРЫЕ ПОЛЬЗОВАТЕЛЬ ВЫБРАЛ КАК ДЕФОЛТНЫЕ */
	public function getDefaultVoms(){
	
		return db::get()->getColIndexed('SELECT project_id, default_vo FROM user_allowed_projects WHERE uid='.$this->id);
	}
	
	public function getAllowedSoftware(){
		
		return db::get()->getAllIndexed('SELECT s.* FROM '.Software::TABLE.' s JOIN user_allowed_software us ON us.software_id=s.id WHERE us.uid='.$this->id, 'id');
	}
	
	public function myproxyLogon($server, $port, $login, $password, $ttl){
		
		// $debug = TRUE;
		$debug = FALSE;
		$tmpfile = tempnam("/tmp", "x509_mp_");
		
		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		return myproxy_logon($server, $port, $login, $password, $ttl, $tmpfile, $debug);
	}

	public function resetMyproxyExpireDate(){
		
		$this->setField('myproxy_expire_date', 0);
		$this->_save();
	}
	
	public function getDir(){
		
		return FS_ROOT.'files/user/'.$this->id.'/';
	}
	
	/* ПОЛУЧИТЬ АВТОРИЗАЦИОННЫЕ ДАННЫЕ ДЛЯ MYPROXY */
	public function getMyproxyLoginData(){
		
			if($this->getField('myproxy_manual_login') || $this->getField('myproxy_expire_date') < time()){
				$this->resetMyproxyExpireDate();
				throw new Exception('Требуется логин и пароль myproxy');
				return FALSE;
			}
			
			return array(
				'serverId' => $this->getField('myproxy_server_id'),
				'login'    => $this->getField('myproxy_login'),
				'password' => $this->getFieldPrepared('myproxy_password'),
				'lifetime'  => $this->getField('myproxy_expire_date') - time(),
			);
	}
}


class UserCollection extends GenericObjectCollection{

	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'email' => 'email',
		'surname' => array('surname _DIR_, name _DIR_, patronymic _DIR_', 'ФИО, пол'),
		'birthdate' => 'Дата рождения',
		'address' => array('country _DIR_, city _DIR_', 'Адрес'),
		'level' => 'Права',
		'regdate' => 'Дата регистрации',
	);
	
	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.User::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = User::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>