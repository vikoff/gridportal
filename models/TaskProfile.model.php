<?php

class TaskProfile extends GenericObject{
	
	const TABLE = 'task_profiles';
	
	const NOT_FOUND_MESSAGE = 'Профиль не наден';
	
	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new TaskProfile(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new TaskProfile($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new TaskProfile($id, self::INIT_EXISTS_FORCE, $fieldvalues);
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
	
		$data['create_date'] = YDate::loadTimestamp($data['create_date'])->getStrDateShortTime();
		return $data;
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(array(),
			array(
                'name' => array('required' => true, 'length' => array('max' => '255')),
                'project_id' => array('required' => true, 'settype' => 'int'),
            ));
			$this->validator->setFieldTitles(array(
				'is_user_defined' => 'is_user_defined',
				'name' => 'Имя профиля',
				'project_id' => 'Проект',
			));
		}
		
		return $this->validator;
	}
		
	/** ПРЕ-ВАЛИДАЦИЯ ДАННЫХ */
	public function preValidation(&$data){}
	
	/** ПОСТ-ВАЛИДАЦИЯ ДАННЫХ */
	public function postValidation(&$data){
		
		$data['uid'] = USER_AUTH_ID;
		$data['is_user_defined'] = !App::$adminMode;
		
		if($this->isNewObj){
			$data['create_date'] = time();
		}
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ */
	public function afterSave($data){
		
	}
	
	/** ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА */
	public function beforeDestroy(){
	
		// удаление файлов задачи
		$taskdir = $this->getFilesDir();
		`rm -rf $taskdir`;
	}
	
	/** ПОЛУЧИТЬ СПИСОК ВСЕХ ФАЙЛОВ, СВЯЗАННЫХ С ЗАДАЧЕЙ */
	public function getAllFilesList(){
			
		$taskDir = $this->getFilesDir();
		
		if(!is_dir($taskDir))
			return array();
		
		$elms = array();
		foreach(scandir($taskDir) as $elm)
			if(!in_array($elm, array('.', '..')))
				$elms[] = $elm;
		
		return $elms;
	}
	
	public function getFilesDir(){
		
		return FS_ROOT.'files/task_profiles/'.$this->id.'/';
	}
	
	/**
	 * ПОЛУЧЕНИЕ/СОХРАНЕНИЕ ФАКТА НАЛИЧИЯ GRIDJOB ФАЙЛА
	 * @param null|bool $save
	 *		если null - функция возвращает факт наличия gridjob файла
	 *		если bool - функция сохраняет факт наличия gridjob файла
	 * @return bool факт наличия gridjob файла
	 */
	public function hasGridjobFile($save = null){
		
		// сохранение (если надо)
		if(!is_null($save)){
			$this->setField('is_gridjob_loaded', $save ? '1' : '0');
			$this->_save();
		}
		
		return $this->getField('is_gridjob_loaded');
	}

	public function getGridjobTaskname(){
		
		$xrsl = file_get_contents($this->getFilesDir().'nordujob');
		if(preg_match('/\(\s*jobname\s*=\s*"(.+)"\s*\)/', $xrsl, $matches)){
			return $matches[1];
		} else {
			return null;
		}
	}

}

class TaskProfileCollection extends GenericObjectCollection{
	
	protected $_filters = array();
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'is_user_defined' => 'is_user_defined',
		'uid' => 'uid',
		'name' => 'Имя профиля',
		'project_id' => 'Проект',
		'create_date' => 'create_date',
	);
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function load($filters = array()){
			
		$instance = new TaskProfileCollection($filters);
		return $instance;
	}
	
	public function __construct($filters = array()){
		
		$this->_filters = $filters;
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.TaskProfile::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = TaskProfile::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	public function getAll(){
		
		$condition = (!empty($this->_filters['project_id']))
			? ' WHERE project_id='.(int)$this->_filters['project_id']
			: '';
		
		$data = db::get()->getAllIndexed('SELECT * FROM '.TaskProfile::TABLE.' '.$condition, 'id');
		
		foreach($data as &$row)
			$row = TaskProfile::forceLoad($row['id'], $row)->getAllFieldsPrepared();
			
		return $data;
	}
	
}

?>