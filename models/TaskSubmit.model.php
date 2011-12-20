<?php

class TaskSubmit extends GenericObject{
	
	const TABLE = 'task_submits';
	
	const NOT_FOUND_MESSAGE = 'Запуск задачи не найден';

	private $_uid = 0;
	
	private $_log = array();
	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new TaskSubmit(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new TaskSubmit($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new TaskSubmit($id, self::INIT_EXISTS_FORCE, $fieldvalues);
	}
	
	public function setUid($uid){
		
		$this->_uid = $uid;
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
	
		$data['status_str'] = $data['status']
			? ($data['is_submitted']
				? TaskStatus::get()->statuses[$data['status']]['title']
				: 'task.state.inqueue')
			: 'task.state.undefined';
		$data['start_date_str'] = YDate::loadTimestamp($data['start_date'])->getStrDateShortTime();
		$data['finish_date_str'] = YDate::loadTimestamp($data['finish_date'])->getStrDateShortTime();
		$data['fullname'] = self::getSubmitName($data['name'], $data['gridjob_name'], $data['index']);
		
		$data['actions'] = array(
			// остановка задачи доступна для выполняющихся в данный момент
			'stop' => !empty($data['jobid']) && $data['is_completed'] == 0,
			// получение результатов доступно только для выполненных задач
			'get_results' => $data['is_completed'] == 1 && !$data['is_fetched'],
			// перейти к анализу
			'to_analyze' => $data['is_completed'] == 1 && $data['is_fetched'],
			// удаление доступно для всех задач
			'delete' => empty($data['jobid']) || in_array($data['is_completed'], array(1, 2)),
		);
		
		return $data;
	}
	
	public function save($data){
		
		$this->setFields($data);
		$this->_save();
		$this->afterSave($data);
		return $this->id;
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(array(),
			array(
                'set_id' => array('settype' => 'int'),
                'index' => array('settype' => 'int'),
                'status' => array('settype' => 'int'),
                'is_submitted' => array('settype' => 'int'),
                'is_completed' => array('settype' => 'int'),
                'is_fetched' => array('settype' => 'bool'),
                'start_date' => array('settype' => 'int'),
                'finish_date' => array('settype' => 'int'),
            ));
			$this->validator->setFieldTitles(array(
				'id' => 'id',
				'set_id' => 'Набор',
				'index' => 'Порядковый номер',
				'status' => 'Сатус',
				'is_submitted' => 'Отправлена',
				'is_completed' => 'Завершена',
				'is_fetched' => 'Получена',
				'start_date' => 'Дата запуска',
				'finish_date' => 'Дата завершения',
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
		
		if($this->isNewObj)
			$data['create_date'] = time();
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ */
	public function afterSave($data){
		
		$dir = $this->getFilesDir();
		if (!is_dir($dir))
			mkdir($dir, 0777, TRUE);
		
		if (!is_dir($dir.'src/'))
			mkdir($dir.'src/', 0777, TRUE);
	}
	
	/** ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА */
	public function beforeDestroy(){
	
		// удаление файлов задачи
		$filesDir = $this->getFilesDir();
		`rm -rf $filesDir`;
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ УДАЛЕНИЯ ОБЪЕКТА */
	public function afterDestroy(){
		
		// обновление количества сабмитов у сета
		TaskSet::load($this->getField('set_id'))->updateNumSubmits();
	}
	
	public function submit(MyproxyConnector $connector, $preferedServer, $taskSetData){
		
		// подключение myproxy
		if (!$connector->connect()){
			
			$this->setError($connector->errcode == 104
				? Lng::get('myproxy.auth-fail', array('<a href="'.href('profile/edit#/temporary-cert').'">', '</a>'))
				: $connector->getHumanReadableMsg($connector->errcode)
			);
			$this->setError('');
			$this->setError($connector->errcode.' '.$connector->errmsg);
			return FALSE;
		}
			
		$this->log(Lng::get('Task.model.myproxy-success-proceed'));
		
		
		$env = "/bin/env";
		$ngsub = "/opt/nordugrid-8.1/bin/ngsub";
		$taskdir = $this->getFilesDir().'src/';
		$ngjob = $this->getNgjobStr();
		
		// подстановка правильного имени в gridjob файл
		$realGridjobName = self::getSubmitName($taskSetData['name'], $taskSetData['gridjob_name'], $this->getField('index'));
		// ( jobname="Test job on CrimeaEco VO" )
		$ngjob = preg_replace('/\(\s*jobname\s*=\s*".+"\s*\)/', '( jobname="'.$realGridjobName.'" )', $ngjob);
		//                       (   jobname   =   "**"    )
		// echo $ngjob; die;
		
		$command  = ''
			." cd ".$taskdir. " && "
			.$env . " X509_USER_PROXY=".$connector->tmpfile." "
			.$ngsub . " -d2 -o /home/apache/.ngjobs"
			.(!empty($preferedServer) ? ' -c '.escapeshellarg($preferedServer) : '')
			." -e ". escapeshellarg(stripslashes($ngjob))." 2>&1";
		
		// echo $command; die;
		$this->log("run ngsub: $command ...");
		
		// выполнение команды ngsub
		exec($command, $outputArr, $retval);
		
		$response = implode("\n", $outputArr);
		$this->log($response);
		
		if($retval == 0){
		
			$this->log(Lng::get('Task.model.rusk-run-success'));
			
			$jobid = preg_match('/(gsiftp:\/\/\S+\d+)/', $response, $matches) ? $matches[1] : null;
			if(!empty($jobid)){
				$this->setField('jobid', $jobid);
				$this->setField('prefered_server', $preferedServer);
				$this->setField('start_date', time());
				$this->setField('is_submitted', 1);
				$this->_save();
			}else{
				$this->setError('Не удалось сохранить jobid задачи');
			}
				
			return TRUE;
			
		}else{
			$this->setError('Скрипт вернул код ошибки: '.$retval);
			return FALSE;
		}
		
	}
	
	public function stop(MyproxyConnector $connector){
		
		// подключение myproxy
		if (!$connector->connect()){
			
			$this->setError($connector->errcode == 104
				? 'Авторизация не пройдена. Уточните параметры в <a href="'.href('profile/edit#/temporary-cert').'">профиле</a>, или введите заново параметры вручную.'
				: $connector->getHumanReadableMsg($connector->errcode)
			);
			$this->setError('');
			$this->setError($connector->errcode.' '.$connector->errmsg);
			return FALSE;
		}
			
		$this->log(Lng::get('Task.model.myproxy-success-proceed'));
		
		$env = "/bin/env";
		$ngkill = "/opt/nordugrid-8.1/bin/ngkill";
		
		$command  = ''
			.$env . " X509_USER_PROXY=".$connector->tmpfile." "
			.$ngkill . " -d2 ".escapeshellarg($this->getField('jobid'))." 2>&1";
		
		$this->log("Запуск ngkill: $command ...");
		
		// выполнение команды ngsub
		exec($command, $outputArr, $retval);
		
		$response = implode("\n", $outputArr);
		$this->log($response);
		
		if($retval == 0){
			
			$this->destroy();
			$this->log("Задача успешно остановлена!");
				
			return TRUE;
			
		}else{
			$this->setError('Скрипт вернул код ошибки: '.$retval);
			return FALSE;
		}
	}
	
	public function getResults(MyproxyConnector $connector){
		
		// подключение myproxy
		if (!$connector->connect()){
			
			$this->setError($connector->errcode == 104
				? 'Авторизация не пройдена. Уточните параметры в <a href="'.href('profile/edit#/temporary-cert').'">профиле</a>, или введите заново параметры вручную.'
				: $connector->getHumanReadableMsg($connector->errcode)
			);
			$this->setError('');
			$this->setError($connector->errcode.' '.$connector->errmsg);
			return FALSE;
		}
			
		$this->log('Запрос Майпрокси удачно! Продолжаем:');
		
		$taskdir = $this->getFilesDir().'results/';
		$env = "/bin/env";
		$ngget = "/opt/nordugrid-8.1/bin/ngget";
		
		if (!is_dir($taskdir))
			mkdir($taskdir, 0777, TRUE);
			
		$command  = ''
			." cd ".$taskdir. " && "
			.$env . " X509_USER_PROXY=".$connector->tmpfile." "
			.$ngget . " -d2 ".escapeshellarg($this->getField('jobid'))." 2>&1";

		$this->log("Запуск ngget: $command ...");
		
		// выполнение команды ngsub
		exec($command, $outputArr, $retval);
		
		$response = implode("\n", $outputArr);
		$this->log($response);
		
		if($retval == 0){
		
			$this->setField('is_fetched', TRUE);
			$this->_save();
			
			$this->log("Файлы задачи получены!");
			return TRUE;
			
		}else{
			$this->setError('Скрипт вернул код ошибки: '.$retval);
			return FALSE;
		}
			
	}
	
	public function log($msg){
	
		$this->_log[] = $msg;
	}
	
	public function getLog(){
	
		return implode('<br />', $this->_log);
	}
	
	public function getNgjobStr(){
		
		return str_replace("\r\n", "\n", file_get_contents($this->getFilesDir().'src/nordujob'));
		
	//	return "&(executable=/bin/sleep)(arguments=1000)(jobname='GJSWI sleep test')";
		
		// $ngjob = "&\n";
		// foreach(unserialize($this->getField('xrsl_command')) as $k => $v)
			// $ngjob .= '('.$k.'='.$v.")\n";
		
		// return $ngjob;
	}

	public function getFilesDir(){
		
		return FS_ROOT.'files/users/'.$this->getUid().'/task_sets/'.$this->getField('set_id').'/submits/'.$this->id.'/';
	}
	
	/** ПОЛУЧИТЬ СПИСОК ИСХОДНЫХ ФАЙЛОВ ЗАДАЧИ */
	public function getSrcFiles(){
			
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
	
	/** ПОЛУЧИТЬ ФАЙЛЫ РЕЗУЛЬТАТОВ */
	public function getResultFiles($_path){
		
		$path = $rootDir = $this->getFilesDir().'results/';
		$isRootDir = TRUE;
		
		if(!empty($_path)){
			$realpath = realpath($path.$_path);
			if(substr($realpath, -1) != '/')
				$realpath .= '/';
			if(strpos($realpath, $path) === 0){
				$isRootDir = $realpath == $path;
				$path = $realpath;
			} else {
				Messenger::get()->addError(Lng::get('access denided'));
			}
		}
		
		$relpath = str_replace($rootDir, '', $path);
		
		$files = array(
			'curpath' => $path,
			'relpath' => $relpath,
			'isRootDir' => $isRootDir,
			'dirs'  => array(),
			'files' => array(),
		);
		
		if(!is_dir($path))
			return $files;

		foreach(scandir($path) as $elm){
			
			if($elm == '.' || $elm == '..')
				continue;
			
			$isDir = is_dir($path.$elm);
			$files[$isDir ? 'dirs' : 'files'][] = $elm;
		}
		
		// echo '<pre>'; print_r($files); die;
		return $files;
	}
	
	/** СКАЧАТЬ ПАПКУ */
	public function downloadDir($_path){
		
		$path = $rootDir = $this->getFilesDir().'results/';
		$realpath = realpath($path.$_path);
		if(substr($realpath, -1) != '/')
			$realpath .= '/';
		
		if(strpos($realpath, $rootDir) !== 0){
			echo $path.'<br />';
			echo $realpath ; die;
			FrontendViewer::get()->error404();
		}
		
		$archive = substr($realpath, 0, -1).'.zip';
		
		// echo ('/usr/bin/zip -r -9 '.escapeshellarg(basename($archive)).' '.escapeshellarg($realpath)); die;
		exec('/usr/bin/zip -r -9 '.escapeshellarg($archive).' '.escapeshellarg($realpath));
		
		if (!file_exists($archive))
			die('archive file not found');
			
		header('Expires: 0');
		header('Cache-Control: private');
		header('Pragma: cache');
		header('Content-type: application/download');
		header('Content-Disposition: attachment; filename='.basename($archive));
		
		readfile($archive);
		unlink($archive);
		exit;
	}
	
	/** СКАЧАТЬ ФАЙЛ */
	public function downloadFile($relname){
		
		$rootDir = $this->getFilesDir().'results/';
		$fullname = realpath($rootDir.$relname);
		
		if(strpos($fullname, $rootDir) !== 0){
			echo $path.'<br />';
			echo $fullname ; die;
			FrontendViewer::get()->error404();
		}
		
		if (!file_exists($fullname))
			die('file not found');
		
		header('Content-type: text/plain; charset=utf-8');
		
		// header('Expires: 0');
		// header('Cache-Control: private');
		// header('Pragma: cache');
		// header('Content-type: application/download');
		// header('Content-Disposition: attachment; filename='.basename($fullname));
		
		readfile($fullname);
		exit;
	}
	
	/**
	 * @TODO: раньше uid хранился только в таблице task_sets, а сейчас я денормализовал
	 * и добавил колонку uid в task_sets. Надо переделать все вхождения на новый вариант
	 */
	public function getUid(){
		
		return $this->getField('uid');
	}
	
	public function dbGetRow(){
		
		$data = db::get()->getRow(
			"SELECT sub.*, s.name, s.gridjob_name FROM ".self::TABLE." sub
			JOIN ".TaskSet::TABLE." s ON s.id=sub.set_id
			WHERE sub.id='".$this->id."'");
		
		return $data;
	}

	public static function getSubmitName($taskName, $taskNordujobName, $index) {
		
		$nameArr = array();
		if ($taskName)
			$nameArr[] = $taskName;
		if ($taskNordujobName)
			$nameArr[] = $taskNordujobName;
		$nameArr[] = 'rc'.$index;
		
		$name = implode('_', $nameArr);
		$name = Tools::translit($name);
		$name = preg_replace('/\s/', '_', $name);
		return $name;
	}
	
}

class TaskSubmitCollection extends GenericObjectCollection{
	
	protected $_filters = array();
	
	/**
	 * получить поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	public function _getSortableFieldsTitles(){
		return array(
			'id' => array('t.id _DIR_', 'id'),
			'name' => array('`index` _DIR_', Lng::get('tasklist.name')),
			'jobid' => 'JobID',
			'set_id' => 'Набор',
			'index' => 'Порядковый номер',
			'status' => 'Статус',
			'is_submitted' => 'Отправлена',
			'is_completed' => 'Завершена',
			'is_fetched' => 'Получена',
			'create_date' => 'Дата создания',
			'start_date' => 'Дата запуска',
			'finish_date' => 'Дата завершения',
		);
	}
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function load($filters = array()){
			
		$instance = new TaskSubmitCollection($filters);
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
		if(!empty($this->filters['ids']))
			$whereArr[] = 't.id IN('.$this->filters['ids'].')';
		if(!empty($this->filters['set_id']))
			$whereArr[] = 'set_id='.$this->filters['set_id'];
		if(!empty($this->filters['set_id']))
			$whereArr[] = 't.set_id IN('.implode(',', (array)$this->filters['set_id']).')';
		
		$whereStr = !empty($whereArr) ? ' WHERE '.implode(' AND ', $whereArr) : '';
		
		$sorter = new Sorter('s.id', 'DESC', $this->_getSortableFieldsTitles());
		$paginator = new Paginator('sql', array(
			't.*, sets.name, sets.gridjob_name',
			'FROM '.TaskSubmit::TABLE.' t
			LEFT JOIN task_states s ON t.status=s.id 
			JOIN '.TaskSet::TABLE.' sets ON sets.id=t.set_id
			'.$whereStr.'
			ORDER BY '.$sorter->getOrderBy()), '~50');
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		// echo '<pre>'; print_r($data);
		foreach($data as &$row)
			$row = TaskSubmit::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	public function getAll(){
		
		$whereArr = array();
		if(!empty($this->filters['uid']))
			$whereArr[] = 't.uid='.$this->filters['uid'];
		if(!empty($this->filters['ids']))
			$whereArr[] = 't.id IN('.implode(',', $this->filters['ids']).')';
		if(!empty($this->filters['set_id']))
			$whereArr[] = 't.set_id IN('.implode(',', (array)$this->filters['set_id']).')';
		
		$whereStr = !empty($whereArr) ? ' WHERE '.implode(' AND ', $whereArr) : '';
		
		$data = db::get()->getAllIndexed('
			SELECT t.*, s.title AS state_title, sets.name, sets.gridjob_name
			FROM '.TaskSubmit::TABLE.' t
			JOIN '.TaskSet::TABLE.' sets ON sets.id=t.set_id
			LEFT JOIN task_states s ON t.status=s.id '.$whereStr,
			'id',
			array()
		);
		
		// echo '<pre>'; print_r($data); die;
		foreach($data as &$row)
			$row = TaskSubmit::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		return $data;
	}
	
	public function getFetchedSubmits(){
		
		$db = db::get();
		$data = $db->getAll('
			SELECT sub.*, s.uid, s.name, s.gridjob_name FROM '.TaskSubmit::TABLE.' sub
			JOIN '.TaskSet::TABLE.' s ON s.id=sub.set_id
			WHERE sub.is_fetched=true '.(!empty($this->filters['uid']) ? ' AND s.uid='.$this->filters['uid'] : '').'
			ORDER BY `index`');
		
		foreach($data as &$row)
			$row = TaskSubmit::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		return $data;
	}
	
	public function getTasksBySet($set_id, $short = FALSE){
		
		$fields = $short
			? 't.id, t.status, states.title'
			: 't.*, sets.name, sets.gridjob_name';
			
		$data = db::get()->getAll('
			SELECT '.$fields.'
			FROM '.TaskSubmit::TABLE.' t
			JOIN '.TaskSet::TABLE.' sets ON sets.id=t.set_id
			LEFT JOIN task_states states ON states.id=t.status
			WHERE t.set_id='.$set_id.' ORDER BY `index` DESC');
		
		if (!$short)
			foreach($data as &$row)
				$row = TaskSubmit::forceLoad($row['id'], $row)->getAllFieldsPrepared();
			
		return $data;
	}
	
	public function getFileTree($_path){
		
		$path = $rootDir = CurUser::get()->getTasksDir();
		$isRootDir = TRUE;
		
		if(!empty($_path)){
			$realpath = realpath($path.$_path);
			if(substr($realpath, -1) != '/')
				$realpath .= '/';
			if(strpos($realpath, $path) === 0){
				$path = $realpath;
				$isRootDir = FALSE;
			} else {
				Messenger::get()->addError(Lng::get('access denided'));
			}
		}
		
		$relpath = str_replace($rootDir, '', $path);
		$allTasks = $this->getAll();
		
		$files = array(
			'curpath' => $path,
			'relpath' => $relpath,
			'isRootDir' => $isRootDir,
			'dirs'  => array(),
			'files' => array(),
		);
		
		if(!is_dir($path))
			return $files;

		foreach(scandir($path) as $elm){
			
			if($elm == '.' || $elm == '..')
				continue;
			
			$isDir = is_dir($path.$elm);
			$title = $isDir && isset($allTasks[$elm]) ? '<span title="'.$allTasks[$elm]['jobid'].'">'.$allTasks[$elm]['name'].'</span>' : $elm;
			$files[$isDir ? 'dirs' : 'files'][] = array(
				'name' => $elm,
				'title' => $title,
			);
		}
		
		
		// echo '<pre>'; print_r($files); die;
		return $files;
	}
	
	public function downloadDir($_path){
		
		$path = $rootDir = CurUser::get()->getTasksDir();
		$realpath = realpath($path.$_path);
		if(substr($realpath, -1) != '/')
			$realpath .= '/';
		
		if(strpos($realpath, $path) !== 0){
			echo $path.'<br />';
			echo $realpath ; die;
			FrontendViewer::get()->error404();
		}
		
		$archive = substr($realpath, 0, -1).'.zip';
		exec('/usr/bin/zip -r -9 '.escapeshellarg($archive).' '.escapeshellarg($realpath));
		
		if (!file_exists($archive))
			die('archive file not found');
			
		 header('Expires: 0');
		 header('Cache-Control: private');
		 header('Pragma: cache');
		 header('Content-type: application/download');
	     header('Content-Disposition: attachment; filename='.basename($archive));
		
		readfile($archive);
		unlink($archive);
		exit;
	}

	public function delete(){
		
		$whereArr = array();
		if(!empty($this->filters['uid']))
			$whereArr[] = 't.uid='.$this->filters['uid'];
		if(!empty($this->filters['ids']))
			$whereArr[] = 't.id IN('.implode(',', $this->filters['ids']).')';
		
		$whereStr = !empty($whereArr) ? ' WHERE '.implode(' AND ', $whereArr) : '';
		
		$data = db::get()->getAllIndexed('
			SELECT t.*, s.title AS state_title, sets.name, sets.gridjob_name
			FROM '.TaskSubmit::TABLE.' t
			JOIN '.TaskSet::TABLE.' sets ON sets.id=t.set_id
			LEFT JOIN task_states s ON t.status=s.id '.$whereStr,
			'id',
			array()
		);
		
		// echo '<pre>'; print_r($data); die;
		foreach($data as &$row)
			TaskSubmit::forceLoad($row['id'], $row)->destroy();
			
		return TRUE;
	}
	
}

?>