<?php

class TaskSet extends GenericObject{
	
	const TABLE = 'task_sets';
	const TABLE_QUEUE = 'task_submit_queue';
	
	const NOT_FOUND_MESSAGE = 'Задача не найдена';
	
	const FILETYPE_NORDUJOB = 'nordujob';
	const FILETYPE_FDS = 'fds';
	
	public $submits = array();
	
	public $firstSubmit = null;

	
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
	
	public static function getFileType($fullname){
		
		$basename = basename($fullname);
		
		if($basename == 'nordujob')
			return self::FILETYPE_NORDUJOB;
		
		$ext = Tools::getExt($basename);
		switch ($ext) {
			// case 'fds': return self::FILETYPE_FDS;
			default: null;
		}
	}
	
	public static function getFileConstructor($type, $fullname){
		
		switch($type){
			
			case self::FILETYPE_NORDUJOB:
				require_once(FS_ROOT.'models/fileConstructors/NordujobFileConstructor.php');
				return new NordujobFileConstructor($fullname);
				
			case self::FILETYPE_FDS:
				require_once(FS_ROOT.'models/fileConstructors/FdsFileConstructor.php');
				return new FdsFileConstructor($fullname);
			
			default: trigger_error('Неизвестный тип файла "'.$type.'"', E_USER_ERROR);
		}
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
			if ($this->isNewObj && $data['profile_id']) {
				$profileInstance = $this->_checkProfile($data['profile_id'], $data['project_id']);
				if ($profileInstance->getField('is_gridjob_loaded')) {
					$data['gridjob_name'] = $profileInstance->getGridjobTaskname();
					$data['is_gridjob_loaded'] = 1;
				}
				$this->additData['profile_instance'] = $profileInstance;
			}
		}		
		catch(Exception $e){
			$this->setError($e->getMessage());
			return FALSE;
		}
		
		$data['uid'] = USER_AUTH_ID;
		$data['num_submits'] = 0;
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
	
	/** ПОЛУЧИТЬ ПОЛНЫЙ ПУТЬ ФАЙЛА ПО ИМЕНИ */
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
	 *		если bool - функция сохраняет факт наличия gridjob файла,
	 *                  а так же парсит и сохраняет имя задачи (или удаляет)
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
	
	public function submit(MyproxyConnector $connector, $preferServer = ''){
		
		$basedir = $this->getFilesDir().'src/';
		$multipliers = array();
		
		// поиск всех множителей в файле
		foreach($this->getAllFilesList() as $f) {
			if ( $ftype = TaskSet::getFileType($f) ) {
				$multipliers = array_merge(
					$multipliers,
					TaskSet::getFileConstructor($ftype, $this->getValidFileName($f))->getMultipliers()
				);
			}
		}
		
		// создание субмиттера
		$submitter = new BatchSubmitter();
		foreach($multipliers as $mult)
			$submitter->addMultiplier($mult['file'], $mult['row'], $mult['values'], $mult['valuesStr']);
		
		// создание первого экземпляра субмита
		$this->firstSubmit = $this->_createSubmitInstance( $submitter->getNextCombination() );
		$this->submits[] = $this->firstSubmit;
		
		// запуск первого субмита
		$success = $this->firstSubmit->submit($connector, $preferServer, $this->getAllFields());

		if ($success) {
			
			$db = db::get();
			
			// создание остальных субмитов
			while ($combination = $submitter->getNextCombination()) {
				$submit = $this->_createSubmitInstance($combination);
				$db->insert(self::TABLE_QUEUE, array(
					'trigger_task_id'   => $this->firstSubmit->id,
					'dependent_task_id' => $submit->id,
				));
				$this->submits[] = $submit;
			}
			
			$this->updateNumSubmits();
			return array(
				'success' => TRUE,
				'queue_length' => $submitter->numCombinations - 1,
			);
			
		} else {
			
			$this->firstSubmit->destroy();
			$this->updateNumSubmits();
			return FALSE;
		}
	}
	
	private function _createSubmitInstance($combination){
		
		// echo '<pre>'; print_r($combination); die;
		
		$taskSubmit = TaskSubmit::create();
		$taskSubmit->save(array(
			'set_id' => $this->id,
			'uid' => $this->getField('uid'),
			'index' => $this->getLastSubmitIndex() + 1,
			'status' => NULL,
			'is_submitted' => 0,
			'is_completed' => FALSE,
			'is_fetched' => FALSE,
		));
		
		// копирование файлов
		$src = $this->getFilesDir().'src/*';
		$dst = $taskSubmit->getFilesDir().'src/';
		`cp $src $dst`;
		
		// заполнение множителей
		foreach($taskSubmit->getSrcFiles() as $f) {
			if ( $ftype = TaskSet::getFileType($f) ) {
				$fullname = $taskSubmit->getValidFileName($f);
				if ($fullname && isset($combination[$f])) {
					file_put_contents(
						$fullname,
						TaskSet::getFileConstructor($ftype, $fullname)->getCombination($combination[$f])
					);
				}
			}
		}
		
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
	
	public function dbGetRow(){
		
		return db::get()->getRow(
			'SELECT s.*, proj.name AS project_name, prof.name AS profile_name FROM '.self::TABLE.' s
			LEFT JOIN '.Project::TABLE.' proj ON proj.id=s.project_id
			LEFT JOIN '.TaskProfile::TABLE.' prof ON prof.id=s.profile_id
			WHERE s.id='.$this->id
		);
	}
	
}

class TaskSetCollection extends GenericObjectCollection{
	
	protected $_filters = array();
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected function _getSortableFieldsTitles(){
		
		return array(
			'id'          => array('s.id', 'id'),
			'uid'         => array('s.uid', 'Пользователь'),
			'project_id'  => Lng::get('taskset.list.project'),
			'profile_id'  => Lng::get('taskset.list.profile'),
			'name'        => array('s.name', Lng::get('taskset.list.name')),
			'num_submits' => Lng::get('taskset.list.num-submits'),
			'create_date' => Lng::get('taskset.list.create-date'),
		);
	}
	
	
	
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
	public function getPaginated($options = array()){
		
		$whereArr = array();
		if(!empty($this->filters['uid']))
			$whereArr[] = 's.uid='.$this->filters['uid'];
			
		$whereStr = !empty($whereArr) ? ' WHERE '.implode(' AND ', $whereArr) : '';
		
		$sqlFields = 's.*, proj.name AS project_name, prof.name AS profile_name';
		$sqlFrom = '
			FROM '.TaskSet::TABLE.' s
			LEFT JOIN '.Project::TABLE.' proj ON proj.id=s.project_id
			LEFT JOIN '.TaskProfile::TABLE.' prof ON prof.id=s.profile_id
		';
		
		if (!empty($options['withUsers'])) {
			
		}
		
		$sorter = new Sorter('s.id', 'DESC', $this->_getSortableFieldsTitles());
		$paginator = new Paginator('sql', array($sqlFields, $sqlFrom.' '.$whereStr.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = TaskSet::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		// echo '<pre>'; print_r($data); die;
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>