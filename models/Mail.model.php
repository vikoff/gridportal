<?php

class Mail extends GenericObject{
	
	const TABLE = 'mail';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';
	
	/** данные для шаблонов */
	protected $_tplVars = array();
	
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
	 * @param int $uid - id пользователя
	 * @param string $template - шаблон письма
	 * @param array $data - массив с ключами (lng, uid*, данные для шаблона)
	 */
	public function send($uid, $template, $data){
		
		$user = User::load($uid);
		
		$lng = $user->getField('lng');
		$tplFile = FS_ROOT.'templates/Mail/templates/'.$template.'.'.$lng.'.php';
		
		if (!file_exists($tplFile))
			trigger_error('Шаблон email сообщения '.$template.' не найден.', E_USER_ERROR);
		
		$tplData = self::getTemplateData($template);
		
		$data['lng'] = $lng;
		$html = $this->getFile($tplFile, $data);
		
		$profile = $user->getFieldPrepared('profile');
		$email = getVar($profile['email']);
		
		if (empty($email)) {
			$this->setError('email не указан');
			return FALSE;
		}
		$this->setFields(array(
			'uid' => $uid,
			'email' => $email,
			'lng' => $lng,
			'template_name' => $template,
			// 'title' => $tplData['title'],
			'text' => $html,
			'add_date' => time(),
		));
		$this->_save();
	}
	
	public static function getTemplateData($template){
		
		$db = db::get();
		return $db->getRow('SELECT * FROM mail_templates WHERE name='.$db->qe($template));
	}
	
	public function getFile($file, $data) {
		
		$this->_tplVars = $data;
		ob_start();
		// $tplContent = include($file);
		include($file);
		$tplContent = ob_get_clean();
		$this->_tplVars = array();
		
		return $tplContent;
	}
	
	public function __get($key){
		
		return isset($this->_tplVars[$key])
			? $this->_tplVars[$key]
			: '';
	}
}

?>