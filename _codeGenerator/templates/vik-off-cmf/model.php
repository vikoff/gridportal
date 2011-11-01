%%%	USE PLACEHOLDERS:
%%%		__CLASSNAME__
%%%		__TABLENAME__
%%%		__VALIDATION_COMMON__
%%%		__VALIDATION_INDIVIDUAL__
%%%		__FIELD_TITLES__
<?php

class __CLASSNAME__ extends GenericObject{
	
	const TABLE = '__TABLENAME__';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';

	
	// ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА)
	public static function create(){
			
		$instance = new __CLASSNAME__(0);
			
		return $instance;
	}
	
	// ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА)
	public static function load($id){
		
		$id = (int)$id;
		
		if(!$id)
			throw new Exception(self::NOT_FOUND_MESSAGE);
			
		$instance = new __CLASSNAME__($id);
		
		if(!$instance->isFound())
			throw new Exception(self::NOT_FOUND_MESSAGE);
			
		return $instance;
		
	}
	
	// ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА)
	public static function forceLoad($id, $fieldvalues){
		
		$instance = new __CLASSNAME__($id);
		$instance->forceLoadData($fieldvalues);
		return $instance;
		
	}
	
	// КОНСТРУКТОР
	public function __construct($id){
		
		parent::__construct($id, self::TABLE);
	}
	
	// ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(__VALIDATION_COMMON__,
			__VALIDATION_INDIVIDUAL__);
			$this->validator->setFieldTitles(__FIELD_TITLES__);
		}
		
		// применение специальных правил для редактирования или добавления объекта
		if($this->isExistsObj){
		
		}
		
		return $this->validator;
	}
	
	// ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ
	public function beforeDisplay($data){
	
		return $data;
	}
		
	// ПРЕ-ВАЛИДАЦИЯ ДАННЫХ
	public function preValidation(&$data){}
	
	// ПОСТ-ВАЛИДАЦИЯ ДАННЫХ
	public function postValidation(&$data){
		
		// $data['author'] = USER_AUTH_ID;
		// $data['modif_date'] = time();
		// if($this->isNewObj)
			// $data['create_date'] = time();
	}
	
	// ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ
	public function afterSave($data){
		
	}
	
	// ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА
	public function beforeDestroy(){
	
	}
	
}

class __CLASSNAME__Collection extends GenericObjectCollection{
	
	// поля, по которым возможна сортировка коллекции
	// каждый ключ должен быть корректным выражением для SQL ORDER BY
	protected $_sortableFieldsTitles = array(__SORTABLE_FIELDS__	);
	
	
	// ТОЧКА ВХОДА В КЛАСС
	public static function Load(){
			
		$instance = new __CLASSNAME__Collection();
		return $instance;
	}

	// ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.__CLASSNAME__::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = __CLASSNAME__::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>