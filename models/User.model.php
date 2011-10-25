<?

class User extends GenericObject{
	
	// пол
	const GENDER_FEMALE 	= 'f';
	const GENDER_MALE		= 'm';
	
	const TABLE = 'users';
	
	const NOT_FOUND_MESSAGE = 'Пользователь не найден';

	
	// ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА)
	public static function Load($id){
		
		$id = (int)$id;
		
		if(!$id)
			throw new Exception(self::NOT_FOUND_MESSAGE);
		
		$instance = new User($id);
		
		if(!$instance->isFound())
			throw new Exception(self::NOT_FOUND_MESSAGE);
			
		return $instance;
		
	}
	
	// ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА)
	public static function forceLoad($id, $fieldvalues){
		
		$instance = new User($id);
		$instance->forceLoadData($fieldvalues);
		return $instance;
		
	}
	
	public function __construct($id = 0){
		
		parent::__construct($id, self::TABLE);
	}

	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(array(
				'required' => array('email', 'password', 'password_confirm', 'surname', 'name', 'patronymic', 'sex', 'birthdate', 'country', 'region', 'settlement', 'city'),
				'strip' => '*',
			),
			array(
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
			$this->validator->delElement(array('email', 'password', 'password_confirm', 'captcha'));
		}
		
		return $this->validator;
	}
	
	// ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ
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
		
		return $data;
	}
	
	public function preValidation(&$data){
		
		$data['birthdate'] = YDate::ymd2date($data['birth_year'], $data['birth_month'], $data['birth_day']);
		
		if($this->isNewObj && self::isEmailInUse($data['email']))
			$this->setError('Данные email-адрес уже используется, возможно Вам следует воспользатся функцией <a href="'.WWW_PREFIX.'profile/forget-password">восстановления учетной записи</a>');
	}
	
	public function postValidation(&$data){
		
		if($this->isNewObj){
			$data['level'] = PERMS_REG;
			$data['active'] = '1';
			$data['regdate'] = time();
		}
	}
	
	public function afterSave($data){
		
	}
	
	// ПОЛУЧИТЬ ИМЯ ПОЛЬЗОВАТЕЛЯ
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
		return implode(' ', $outputArr);
	}
	
	// УСТАНОВИТЬ НОВЫЙ ПАРОЛЬ
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
	
	// УСТАНОВИТЬ НОВЫЙ УРОВЕНЬ ПРАВ
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
	
	// ЗАНЯТ ЛИ EMAIL
	static public function isEmailInUse($email){
	
		return (bool)db::get()->getOne("SELECT COUNT(1) FROM ".self::TABLE." WHERE email='".db::get()->escape($email)."'", FALSE);
	}

	// ПОЛУЧИТЬ СПИСОК ВОЗМОЖНЫХ ПРАВ ПОЛЬЗОВАТЕЛЕЙ
	static public function getPermsList(){
		return array(PERMS_UNREG, PERMS_REG, PERMS_MODERATOR, PERMS_ADMIN, PERMS_SUPERADMIN, PERMS_ROOT);
	}
	
	// ПРОВЕРИТЬ, ИМЕЕТ ЛИ ПОЛЬЗОВАТЕЛЬ УКАЗАННЫЕ ПРАВА
	static public function hasPerm($perm, $curPerms = FALSE){
		
		if($curPerms !== FALSE){
	
			if(!in_array($perm, self::getPermsList(), TRUE))
				trigger_error('Права пользователя "'.$perm.'" не существуют.', E_USER_ERROR);
			if(!in_array($curPerms, self::getPermsList(), TRUE))
				trigger_error('Права пользователя "'.$curPerms.'" не существуют.', E_USER_ERROR);
			
			return ($curPerms < $perm) ? FALSE : TRUE;
		}
		
		if(!defined('USER_AUTH_PERMS'))
			return $perm == 0 ? TRUE : FALSE;
			
		if($perm != 0 && !in_array($perm, self::getPermsList(), TRUE))
			trigger_error('Права пользователя "'.$perm.'" не существуют.', E_USER_ERROR);
		return (USER_AUTH_PERMS < $perm) ? FALSE : TRUE;
	}
	
	// ПОЛУЧИТЬ ТЕКСТОВОЕ НАЗВАНИЕ ПРАВ ПОЛЬЗОВАТЕЛЯ
	static public function getPermName($perm){
		switch($perm){
			case PERMS_UNREG:
				return 'Гость';
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
	
	// ТЕКСТОВОЕ ЗНАЧЕНИЕ ПАРАМЕТРА "ПОЛ ПОЛЬЗОВАТЕЛЯ"
	static public function getGenderString($gender){
		if($gender == self::GENDER_FEMALE)
			return 'женщина';
		elseif($gender == self::GENDER_MALE)
			return 'мужчина';
		else
			return 'не указан';
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
	
	// ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ
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