<?php

class Software extends GenericObject{
	
	const TABLE = 'software';
	
	const NOT_FOUND_MESSAGE = 'Программное обеспечение не найдено';

	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new Software(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new Software($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new Software($id, self::INIT_EXISTS_FORCE, $fieldvalues);
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
                'allowed' => array('name', 'project_id'),
            ),
			array(
                'name' => array('required' => true, 'length' => array('max' => '255')),
                'project_id' => array('required' => true, 'settype' => 'int'),
            ));
			$this->validator->setFieldTitles(array(
				'id' => 'id',
				'name' => 'Название',
				'project_id' => 'Проект',
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

class SoftwareCollection extends GenericObjectCollection{
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected function _getSortableFieldsTitles(){
	
		return array(
			'id' => array('s.id _DIR_', 'id'),
			'name' => array('s.name _DIR_', 'Название'),
			'project_name' => array('p.name _DIR_', 'Проект'),
		);
	}
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function Load(){
			
		$instance = new SoftwareCollection();
		return $instance;
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('s.id', 'DESC', $this->_getSortableFieldsTitles());
		$paginator = new Paginator('sql', array('s.*, p.name AS project_name',
			'FROM '.Software::TABLE.' s LEFT JOIN projects p ON p.id=s.project_id ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		// echo '<pre>'; print_r($data); die;
		
		foreach($data as &$row)
			$row = Software::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	public function getAll(){
		
		$data = db::get()->getAllIndexed('SELECT * FROM '.Software::TABLE, 'id');
		
		foreach($data as &$row)
			$row = Software::forceLoad($row['id'], $row)->getAllFieldsPrepared();
			
		return $data;
	}
	
}

?>