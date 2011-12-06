<?php

class MyproxyServer extends GenericObject{
	
	const TABLE = 'myproxy_servers';
	
	const NOT_FOUND_MESSAGE = 'myproxy сервер не найден';

	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new MyproxyServer(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new MyproxyServer($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new MyproxyServer($id, self::INIT_EXISTS_FORCE, $fieldvalues);
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
                'required' => array('name', 'url', 'port'),
            ),
			array(
                'name' => array('length' => array('max' => '255')),
                'url' => array('length' => array('max' => '65535')),
                'port' => array('settype' => 'int'),
            ));
			$this->validator->setFieldTitles(array(
				'id' => 'id',
				'name' => 'myproxy.name',
				'url' => 'myproxy.url',
				'port' => 'myproxy.port',
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
	
}

class MyproxyServerCollection extends GenericObjectCollection{
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'name' => 'myproxy.name',
		'url' => 'myproxy.url',
		'port' => 'myproxy.port',
	);
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function Load(){
			
		$instance = new MyproxyServerCollection();
		return $instance;
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.MyproxyServer::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = MyproxyServer::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	public function getAll(){
		
		$data = db::get()->getAllIndexed('SELECT * FROM '.MyproxyServer::TABLE, 'id');
		
		foreach($data as &$row)
			$row = MyproxyServer::forceLoad($row['id'], $row)->getAllFieldsPrepared();
			
		return $data;
	}
	
}

?>