<?php

class Mail extends GenericObject{
	
	const TABLE = 'mail';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';

	public $types = array(
		'submit_success' => array(
			'template' => 'submit_success',
			'title' => 'Заголовок',
		),
	);
	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new Mail(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new Mail($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new Mail($id, self::INIT_EXISTS_FORCE, $fieldvalues);
	}
	
	/** СЛУЖЕБНЫЙ МЕТОД (получение констант из родителя) */
	public function getConst($name){
		return constant(__CLASS__.'::'.$name);
	}
	
	/**
	 * ПРОВЕРКА ВОЗМОЖНОСТИ ДОСТУПА К ОБЪЕКТУ
	 * Вызывается автоматически при загрузке существующего объекта
	 * В случае запрета доступа генерирует нужное исключение
	 */
	protected function _accessCheck(){}
	
	/**
	 * ДОЗАГРУЗКА ДАННЫХ
	 * выполняется после основной загрузки данных из БД
	 * и только для существующих объектов
	 * @param array &$data - данные полученные основным запросом
	 * @return void
	 */
	protected function afterLoad(&$data){}
	
	/** ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ */
	public function beforeDisplay($data){
	
		// $data['modif_date'] = YDate::loadTimestamp($data['modif_date'])->getStrDateShortTime();
		// $data['create_date'] = YDate::loadTimestamp($data['create_date'])->getStrDateShortTime();
		return $data;
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(array(
                'allowed' => array('uid', 'email', 'title', 'text', 'add_date', 'send_date'),
            ),
			array(
                'uid' => array('settype' => 'int'),
                'email' => array('length' => array('max' => '255')),
                'title' => array('length' => array('max' => '65535')),
                'text' => array('length' => array('max' => '65535')),
                'add_date' => array('settype' => 'int'),
                'send_date' => array('settype' => 'int'),
            ));
			$this->validator->setFieldTitles(array(
				'id' => 'id',
				'uid' => 'Пользователь',
				'email' => 'Email',
				'title' => 'Заголовок',
				'text' => 'Текст',
				'add_date' => 'Дата добавления',
				'send_date' => 'Дата отправки',
			));
		}
		
		// применение специальных правил для редактирования или добавления объекта
		if($this->isExistsObj){
		
		}
		
		return $this->validator;
	}
	
	/** ПРЕ-ВАЛИДАЦИЯ ДАННЫХ */
	public function preValidation(&$data){}
	
	/** ПОСТ-ВАЛИДАЦИЯ ДАННЫХ */
	public function postValidation(&$data){
		
		// $data['author'] = USER_AUTH_ID;
		// $data['modif_date'] = time();
		// if($this->isNewObj)
			// $data['create_date'] = time();
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ */
	public function afterSave($data){
		
	}
	
	/** ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА */
	public function beforeDestroy(){
	
	}
	
	public function send($type, $data){
		
		$lng = isset($data['lng']) ? $data['lng'] : Lng::get()->getCurLng();
		$tplFile = FS_ROOT.'templates/Mail/templates/'.$type.'.'.$lng.'.php';
		
		if (!file_exists($tplFile))
			trigger_error('Шаблон email сообщения '.$type.' не найден.', E_USER_ERROR);
			
		$tplData = include($tplFile);
		
		$this->setFields(array(
			'uid' => isset($data['uid']) ? (int)$data['uid'] : null,
			'email' => isset($data['email']) ? $data['email'] : null,
			'lng' => $lng,
			'title' => $tplData['title'],
			'text' => $tplData['text'],
			'add_date' => time(),
		));
		$this->_save();
	}
}

class MailCollection extends GenericObjectCollection{
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'uid' => 'Пользователь',
		'email' => 'Email',
		'title' => 'Заголовок',
		'text' => 'Текст',
		'add_date' => 'Дата добавления',
		'send_date' => 'Дата отправки',
	);
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function Load(){
			
		$instance = new MailCollection();
		return $instance;
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.Mail::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = Mail::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>