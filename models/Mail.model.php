<?php

class Mail extends GenericObject{
	
	const TABLE = 'mail';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';
	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new Mail(0, self::INIT_NEW);
	}
	
	/** СЛУЖЕБНЫЙ МЕТОД (получение констант из родителя) */
	public function getConst($name){
		return constant(__CLASS__.'::'.$name);
	}
	
	/**
	 * отправить письмо
	 * @param string $template - шаблон письма
	 * @param array $data - массив с ключами (lng, uid*, данные для шаблона)
	 */
	public function send($template, $data){
		
		$lng = isset($data['lng']) ? $data['lng'] : Lng::get()->getCurLng();
		$tplFile = FS_ROOT.'templates/Mail/templates/'.$template.'.'.$lng.'.php';
		
		if (!file_exists($tplFile))
			trigger_error('Шаблон email сообщения '.$template.' не найден.', E_USER_ERROR);
			
		$tplData = include($tplFile);
		
		
		$profile = User::load($data['uid'])->getFieldPrepared('profile');
		$email = getVar($profile['email']);
		
		if (empty($email)) {
			$this->setError('email не указан');
			return FALSE;
		}
		$this->setFields(array(
			'uid' => $data['uid'],
			'email' => $email,
			'lng' => $lng,
			'title' => $tplData['title'],
			'text' => $tplData['text'],
			'add_date' => time(),
		));
		$this->_save();
	}
}

?>