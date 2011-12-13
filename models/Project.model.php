<?php

class Project extends GenericObject{
	
	const TABLE = 'projects';
	
	const NOT_FOUND_MESSAGE = 'Проект не найден';
	
	const DEFAULT_FILES_PATH = 'files/project_default_files/';

	public $validLngData = array();
	public static $lngFields = array();
	
	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new Project(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new Project($id, self::INIT_EXISTS);
	}
	
	/** ЗАГРУЗИТЬ ОБЪЕКТ СО ВСЕМИ ЯЗЫКАМИ */
	public static function loadWithLngs($id){
		
		$instance = new Project($id, self::INIT_EXISTS);
		$instance->setHiddenField('name' => Lng::getSnippetAllData($instance->getField('name_id')));
		$instance->setHiddenField('text' => Lng::getSnippetAllData($instance->getField('text_id')));
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new Project($id, self::INIT_EXISTS_FORCE, $fieldvalues);
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
		
		$validator = new Validator();
		$validator->rules(array(),
		array(
			'name' => array('length' => array('max' => '255'), 'strip' => true),
			'text' => array('length' => array('max' => '50000')),
		));
		$validator->setFieldTitles(array(
			'id' => 'id',
			'name' => 'Название',
		));
		
		return $validator;
	}
	
	/** ПРЕ-ВАЛИДАЦИЯ ДАННЫХ */
	public function preValidation(&$data){
		
		if(empty($data['voms']) || !is_array($data['voms']))
			$this->setError('Необходимо выбрать хотя бы одну виртуальную организацию');
		else
			$this->_data['voms'] = $data['voms'];
	}
	
	/**
	 * ВАЛИДАЦИЯ ДАННЫХ
	 * Производит некоторые валидационные преобразования.
	 * Если данные не прошли валидацию, сохраняет ошибки в стандартный контейнер
	 * @param &$data - массив данных для валидации
	 * @return void
	 */
	public function validation(&$data){
		
		$validator = $this->getValidator();
		$curLng = Lng::get()->getCurLng();
		
		// группировка данных по языкам и валидация
		foreach(Lng::$allowedLngs as $l){
			
			// дополнительные правила валидации для текущего языка
			$additRules = $l == $curLng ? array('name' => array('required' => TRUE)) : null;
			$this->validLngData[$l] = $validator->validate( getVar($data['lng'][$l], array(), 'array'), $additRules );
			
			if($validator->hasError()){
				$this->setError(
					'<div style="padding: 5px; margin: 5px; border :solid 1px black;">'
						.'<div style="font-weight: bold">'.Lng::getLngTitle($l).'</div>'
						.$validator->getError()
					.'</div>'
				);
			}
			
			$validator->reset();
		}
		
		return !$this->hasError();
	}
	
	/** ПОСТ-ВАЛИДАЦИЯ ДАННЫХ */
	public function postValidation(&$data){
		
		// echo '<pre>'; print_r($data); die;
		// $data['author'] = USER_AUTH_ID;
		// $data['modif_date'] = time();
		
		$data = array();
		// echo '<pre>'; print_r($this->validLngData); die;
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ */
	public function afterSave($data){
		
		$db = db::get();
		$lng = Lng::get();
		
		// сохранение языковых фрагментов
		$lngFields = array('name' => array(), 'text' => array());
		foreach (Lng::$allowedLngs as $l) {
			$lngFields['name'][$l] = $this->validLngData[$l]['name'];
			$lngFields['text'][$l] = $this->validLngData[$l]['text'];
		}
		foreach ($lngFields as $fieldName => $lngData) {
			$key = $fieldName.'_id';
			$lng->save(array(
				'id' => getVar($data[$key]),
				'name' => 'project.'.$this->id.'.'.$fieldName,
				'is_external' => TRUE,
				'text' => $lngData,
			));
			if ($this->isNewlyCreated)
				$this->setField($key, $lng->getLastId());
		}
		if ($this->isNewlyCreated)
			$this->_save();
		
		// сохранение виртуальных организаций
		$db->delete('project_allowed_voms', 'project_id='.$this->id);
		foreach($this->_data['voms'] as $vid)
			$db->insert('project_allowed_voms', array('project_id' => $this->id, 'voms_id' => (int)$vid));
	}
	
	/** ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА */
	public function beforeDestroy(){
		
		$db = db::get();
		$db->delete('project_allowed_voms', 'project_id='.$this->id);
	}
	
	/** ПОЛУЧИТЬ СПИСОК ВИРТУАЛЬНЫХ ОРГАНИЗАЦИЙ ПРОЕКТА */
	public function getVoms(){
		
		return db::get()->getAllIndexed('SELECT * FROM voms v JOIN project_allowed_voms pv ON pv.voms_id=v.id WHERE pv.project_id='.$this->id, 'id');
	}
}

class ProjectCollection extends GenericObjectCollection{
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'name' => 'Название',
	);
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function load(){
			
		$instance = new ProjectCollection();
		return $instance;
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.Project::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = Project::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	public function getAll(){
		
		$data = db::get()->getAllIndexed('SELECT * FROM '.Project::TABLE, 'id');
		
		foreach($data as &$row)
			$row = Project::forceLoad($row['id'], $row)->getAllFieldsPrepared();
			
		return $data;
	}
	
	public function getProjectsVoms(){
		
		$projectVoms = array();
		foreach(db::get()->getAll('SELECT * FROM project_allowed_voms') as $row){
			if(!isset($projectVoms[$row['project_id']]))
				$projectVoms[$row['project_id']] = array();
			$projectVoms[$row['project_id']][] = $row['voms_id'];
		}
		
		return $projectVoms;
	}
	
}

?>