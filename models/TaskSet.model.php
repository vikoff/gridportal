<?php

class TaskSet extends GenericObject{
	
	const TABLE = 'task_sets';
	
	const NOT_FOUND_MESSAGE = 'Задача не найдена';
	
	public $submits = array();
	
	public $lastSubmit = null;

	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new TaskSet(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new TaskSet($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new TaskSet($id, self::INIT_EXISTS_FORCE, $fieldvalues);
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
		$data['create_date_str'] = YDate::loadTimestamp($data['create_date'])->getStrDateShortTime();
		return $data;
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(array(
				'required' => array('project_id', 'name'),
			),
			array(
                'project_id' => array('settype' => 'int'),
                'profile_id' => array('settype' => 'int'),
                'name' => array('length' => array('max' => '255')),
            ));
			$this->validator->setFieldTitles(array(
				'project_id' => 'Проект',
				'profile_id' => 'Профиль',
				'name' => 'Имя набора',
			));
		}
		
		if($this->isExistsObj){
			$this->validator->delElement('profile_id');
		}
		
		return $this->validator;
	}
		
	/** ПРЕ-ВАЛИДАЦИЯ ДАННЫХ */
	public function preValidation(&$data){}
	
	/** ПОСТ-ВАЛИДАЦИЯ ДАННЫХ */
	public function postValidation(&$data){
		
		try{
			// проверка проекта
			$this->_checkProject($data['project_id']);
			
			// проверка уникальности имени для новых задач
			if($this->isNewObj && !$this->isNameUnique($data['name'])){
				$this->setError('задача с таким именем уже существует');
				return FALSE;
			}
			
			// проверка профиля (и сохранение его экземпляра для использования в afterSave)
			if($this->isNewObj && $data['profile_id'])
				$this->additData['profile_instance'] = $this->_checkProfile($data['profile_id'], $data['project_id']);
		}		
		catch(Exception $e){
			$this->setError($e->getMessage());
			return FALSE;
		}
		
		$data['uid'] = USER_AUTH_ID;
		$data['num_submits'] = 0;
		$data['ready_to_start'] = FALSE;
		if($this->isNewObj)
			$data['create_date'] = time();
	}
	
	/** ПРОВЕРКА ПРОЕКТА */
	private function _checkProject($projectId){
		
		$allowedProjects = CurUser::get()->getAllowedProjects();
		if (!isset( $allowedProjects[$projectId] ))
			throw new Exception('Вы не можете создавать задачи в этом проекте.');
	}
	
	/** ПРОВЕРКА ПРОФИЛЯ */
	private function _checkProfile($profileid, $projectId){
		
		$instance = TaskProfile::load($profileid);
		if($instance->getField('project_id') != $projectId)
			throw new Exception('Неверный профиль задачи');
		
		return $instance;
	}
	
	public function isNameUnique($name){
		
		$db = db::get();
		return !$db->getOne('SELECT COUNT(1) FROM '.self::TABLE.' WHERE uid='.USER_AUTH_ID.' AND name='.$db->qe($name));
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ */
	public function afterSave($data){
		
		$dir = $this->getFilesDir();
		
		// создаем папку
		if(!is_dir($dir))
			mkdir($dir, 0777, true);
		
		// скопируем файлы профиля для вновьсозданного объекта (если надо)
		if($this->isNewlyCreated && !empty($this->additData['profile_instance'])){
			
			$src = $this->additData['profile_instance']->getFilesDir().'*';
			$dst = $dir.'src/';
			
			if(!is_dir($dst))
				mkdir($dst, 0777, true);
			
			`cp $src $dst`;
		}
		
	}
	
	/** ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА */
	public function beforeDestroy(){
		
		// удаление сабмитов
		db::get()->delete(TaskSubmit::TABLE, 'set_id='.$this->id);
		
		// удаление файлов
		$filesDir = $this->getFilesDir();
		`rm -rf $filesDir`;
	}
	
	public function getFilesDir(){
		
		return FS_ROOT.'files/users/'.$this->getField('uid').'/task_sets/'.$this->id.'/';
	}
	
	/** ПОЛУЧИТЬ СПИСОК ВСЕХ ФАЙЛОВ, СВЯЗАННЫХ С ЗАДАЧЕЙ */
	public function getAllFilesList(){
			
		$taskDir = $this->getFilesDir().'src/';
		
		if(!is_dir($taskDir))
			return array();
		
		$elms = array();
		foreach(scandir($taskDir) as $elm)
			if(!in_array($elm, array('.', '..')))
				$elms[] = $elm;
		
		return $elms;
	}
	
	public function getValidFileName($fname){
		
		$dir = realpath($this->getFilesDir().'src/').DIRECTORY_SEPARATOR;
		$fullname = realpath($dir.$fname);
		
		if (strpos($fullname, $dir) === FALSE)
			return null;
		
		if (!file_exists($fullname))
			return null;
		
		return $fullname;
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
			if($save){
				$this->setField('is_gridjob_loaded', TRUE);
				$this->setField('gridjob_name', $this->getGridjobTaskname());
			} else {
				$this->setField('is_gridjob_loaded', FALSE);
				$this->setField('gridjob_name', null);
			}
			$this->_save();
		}
		
		return $this->getField('is_gridjob_loaded');
	}
	
	public function parseGridJobFile(){
		
		$file = $this->getFilesDir().'src/nordujob';
		$data = array();
		foreach(file($file) as $row){
			$row = trim($row);
			if($row == '&'){
				$data[] = array('type' => '&', 'string' => $row);
			}elseif(preg_match('/^\(\*(.*)\*\)$/', $row, $matches)){
				$data[] = array('type' => 'comment', 'string' => $matches[1]);
			}elseif(preg_match('/^\((([^=]+)=(.+))\)$/', $row, $matches)){
				$name = $matches[2];
				$data[] = array(
					'type' => 'config',
					'string' => $matches[1],
					'name' => $name,
					'title' => isset($this->_xrslParams[$name]) ? $this->_xrslParams[$name]['title'] : $name,
					'comment' => isset($this->_xrslParams[$name]) ? $this->_xrslParams[$name]['comment'] : '',
					'value' => $matches[3],
					'value_escaped' => htmlentities($matches[3]),
				);
			}else{
				$data[] = array('type' => 'other', 'string' => $row);
			}
		}
		// echo '<pre>'; print_r($data); die;
		return $data;
	}

	public function getGridjobTaskname(){
		
		$xrsl = file_get_contents($this->getFilesDir().'src/nordujob');
		if(preg_match('/\(\s*jobname\s*=\s*"(.+)"\s*\)/', $xrsl, $matches)){
			return $matches[1];
		} else {
			return null;
		}
	}
	
	public function submit($myproxyAuth, $preferServer = ''){
		
		// создание первого экземпляра субмита
		$this->lastSubmit = $this->_createSubmitInstance();
		$this->submits[] = $this->lastSubmit;
		
		if (empty($this->submits)){
			$this->setError('Ни одного субмита не было создано');
			return FALSE;
		}
		
		// запуск первого субмита
		$success = $this->lastSubmit->submit($myproxyAuth, $preferServer);
		
		if ($success) {
			
			// создание всех субмитов
			// foreach(... as $s)
				// $this->_createSubmitInstance();
				
			$this->updateNumSubmits();
			return TRUE;
			
		} else {
			
			$this->updateNumSubmits();
			return FALSE;
		}
	}
	
	private function _createSubmitInstance(){
		
		$taskSubmit = TaskSubmit::create();
		$taskSubmit->setUid($this->getField('uid'));
		$taskSubmit->save(array(
			'set_id' => $this->id,
			'index' => $this->getLastSubmitIndex() + 1,
			'status' => NULL,
			'is_submitted' => FALSE,
			'is_completed' => FALSE,
			'is_fetched' => FALSE,
		));
		
		// копирование файлов
		$src = $this->getFilesDir().'src/*';
		$dst = $taskSubmit->getFilesDir();
		`cp $src $dst`;
		
		return $taskSubmit;
	}
	
	private function getLastSubmitIndex(){
		
		$db = db::get();
		return (int)$db->getOne('SELECT MAX('.$db->quoteFieldName('index').') FROM '.TaskSubmit::TABLE.' WHERE set_id='.$this->id);
	}
	
	public function updateNumSubmits(){
		
		$db = db::get();
		$num = $db->getOne('SELECT COUNT(1) FROM '.TaskSubmit::TABLE.' WHERE set_id='.$this->id);
		$this->setField('num_submits', $num);
		$this->_save();
	}
}

class TaskSetCollection extends GenericObjectCollection{
	
	protected $_filters = array();
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'uid' => 'uid',
		'project_id' => 'Проект',
		'profile_id' => 'Профиль',
		'name' => 'Имя набора',
		'ready_to_start' => 'ready_to_start',
		'num_submits' => 'num_submits',
		'create_date' => 'create_date',
	);
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function load($filters = array()){
			
		$instance = new TaskSetCollection($filters);
		return $instance;
	}

	/**
	 * КОНСТРУКТОР
	 * @param array $filters - список фильтров
	 */
	public function __construct($filters = array()){
		
		$this->filters = $filters;
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$whereArr = array();
		if(!empty($this->filters['uid']))
			$whereArr[] = 'uid='.$this->filters['uid'];
			
		$whereStr = !empty($whereArr) ? ' WHERE '.implode(' AND ', $whereArr) : '';
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.TaskSet::TABLE.' '.$whereStr.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = TaskSet::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>