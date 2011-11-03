<?php

class Task extends GenericObject{
	
	const TABLE = 'tasks';
	
	// СТАТУСЫ ЗАДАЧИ
	const STATE_NEW = 1;
	const STATE_IN_PROCESS = 10;
	const STATE_COMPLETED = 20;
	
	const FILES_DIR = 'files/user_tasks/';
	const TEST_FILES_DIR = 'files/user_tasks/test/';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';
	
	private $_xrslParams = array(
		'executable' => array('title' => 'Command', 'comment' => ''),
	);
	
	// лог
	private $_log = array();
	
	/** обмен данными между скриптами */
	private $_data = array();
	
	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new Task(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new Task($id, self::INIT_EXISTS);
	}

	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new Task($id, self::INIT_EXISTS_FORCE, $fieldvalues);
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
	protected function _accessCheck(){
		
		// if(!App::$adminMode && !$this->getField('published'))
			// throw new Exception403('Доступ к странице ограничен');
	}
	
	// ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(array(),
			array(
                'name' => array('required' => true, 'safe' => TRUE, 'length' => array('max' => '255')),
                'project_id' => array('required' => true, 'settype' => 'int'),
            ));
			$this->validator->setFieldTitles(array(
                'name' => 'Название',
            ));
		}
		
		// применение специальных правил для редактирования или добавления объекта
		if($this->isExistsObj){
			
		}
		
		return $this->validator;
	}
	
	// ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ
	public function beforeDisplay($data){
		
		$ts = TaskStatus::get()->nameIdIndex;
		
		$data['date_str'] = YDate::loadTimestamp($data['date'])->getStrDateShortTime();
		
		$data['actions'] = array(
			// переименование доступно для всех задач
			'rename' => TRUE,
			// файловый менеджер доступен только для нетестовых задач
			'file_manager' => !$data['is_test'],
			// выполнение возможно только для задач у которых есть gridjob файл, и которые не выполняются в данный момент
			'run' => $data['is_gridjob_loaded'],
			// остановка задачи доступна для выполняющихся в данный момент
			'stop' => in_array($data['state'], array( $ts['SUBMITTED'], $ts['ACCEPTING'], $ts['INLRMS'], $ts['INLRMS: R'], $ts['INLRMS: Q'] )),
			// получение результатов доступно только для выполненных задач
			'get_results' => $data['state'] == $ts['FINISHED'],
			// удаление доступно для всех задач
			'delete' => TRUE,
		);
		return $data;
	}
		
	// ПРЕ-ВАЛИДАЦИЯ ДАННЫХ
	public function preValidation(&$data){
			
		$this->_data['useDefaultFiles'] = getVar($data['useDefaultFiles'], false, 'bool');
	}
	
	// ПОСТ-ВАЛИДАЦИЯ ДАННЫХ
	public function postValidation(&$data){
		
		// если проект недоступен пользователю
		$allowedProjects = CurUser::get()->getAllowedProjects();
		if (!isset( $allowedProjects[ $data['project_id'] ] ))
			throw new Exception('Вы не можете создавать задачи в этом проекте.');
		
		$data['uid'] = USER_AUTH_ID;
		$data['date'] = time();
		$data['is_gridjob_loaded'] = 0; // $data['is_test'] ? '1' : '0';
		$data['state'] = TaskStatus::get()->nameIdIndex['PREPARED'];
	}
	
	// ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ
	public function afterSave($data){
		
		if($this->isNewlyCreated){
			
			// создать директорию задачи
			$taskdir = self::FILES_DIR.USER_AUTH_ID.'/'.$this->id.'/';
			mkdir($taskdir, 0777, TRUE);
			mkdir($taskdir.'src/', 0777);
			mkdir($taskdir.'submits/', 0777);
			
			// скопировать предустановленный пакет файлов в задачу
			if($this->_data['useDefaultFiles']){
				$src = FS_ROOT.Project::DEFAULT_FILES_PATH.$this->getField('project_id').'/*';
				$dst = $taskdir.'src/';
				`cp $src $dst`;
			}
			
			// скопировать стандартный nordujob в директорию тестовой задачи
			// if($this->getField('is_test')){
				// copy(self::TEST_FILES_DIR.'nordujob', $taskdir.'nordujob');
			// }
		}
	}
	
	// ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА
	public function beforeDestroy(){
	
		// удаление файлов задачи
		$taskdir = $this->getTaskDir();
		`rm -rf $taskdir`;
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
	
	public function rename($name){
		
		if(empty($name)){
			$this->setError('Имя слишком короткое');
			return FALSE;
		}
		
		$this->setField('name', $name);
		$this->_save();
		return TRUE;
	}
	
	public function parseGridJobFile(){
		
		$file = $this->getField('is_test') ? FS_ROOT.'files/user_tasks/test/nordujob' : FS_ROOT.'files/user_tasks/'.USER_AUTH_ID.'/'.$this->id.'/nordujob';
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
	
	/** ПОЛУЧИТЬ СПИСОК ВСЕХ ФАЙЛОВ, СВЯЗАННЫХ С ЗАДАЧЕЙ */
	public function getAllTaskFilesList(){
			
		$taskDir = $this->getTaskDir().'src/';
		
		if(!is_dir($taskDir))
			return array();
		
		$elms = array();
		foreach(scandir($taskDir) as $elm)
			if(!in_array($elm, array('.', '..')))
				$elms[] = $elm;
		
		return $elms;
	}
	
	/** ПОЛУЧИТЬ СПИСОК ВСЕХ ФАЙЛОВ МОДЕЛЕЙ, СВЯЗАННЫХ С ЗАДАЧЕЙ */
	public function getModelsList(){
	
	}
	
	public function xrsl_save($data){
	
		// echo'<pre>'; print_r($data); die;
		$this->setField('xrsl_command', serialize($data));
		$this->_save();
		
		return TRUE;
	}
	
	/**
	 * ЗАПУСК ЗАДАЧИ
	 * @param string $server - строка server.com:port
	 * @param string $login
	 * @param string $password
	 * @param int $plifetm
	 * @return bool true on success, false on fail
	 */
	public function run($data){
		
		$user = CurUser::get();
		$output = '';
		// $debug = TRUE;
		$debug = FALSE;
		
		// если авторизационные данные тянутся из БД
		if(empty($data)){
			if($user->getField('myproxy_manual_login') || $user->getField('myproxy_expire_date') < time()){
				$user->resetMyproxyExpireDate();
				$this->setError('Требуется логин и пароль myproxy');
				return FALSE;
			}
			
			$serverId = $user->getField('myproxy_server_id');
			$login    = $user->getField('myproxy_login');
			$password = $user->getFieldPrepared('myproxy_password');
			$plifetm  = $user->getField('myproxy_expire_date') - time();
		}
		// если авторазизационные данные пользователь ввел вручную
		else{
			$serverId = (int)$data['serverId'];
			$login    = $data['login'];
			$password = $data['password'];
			$plifetm  = (int)$data['lifetime'];
		}
		
		try{
			$server = MyproxyServer::load($serverId)->getAllFields();
		}catch(Exception $e){
			$this->setError(Lng::get('Task.model.myproxy-server-not-faund'));
			return FALSE;
		}
		
		$tmpfile = tempnam("/tmp", "x509_mp_");
		$env = "/bin/env";
		$ngsub = "/opt/nordugrid-8.1/bin/ngsub";
		
		$ngjob = $this->getNgjobStr();

		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		$myProxyIsLogged = myproxy_logon($server['url'], $server['port'], $login, $password, $plifetm, $tmpfile, $debug);
		
		if(!$myProxyIsLogged){
			
			if(!$user->getField('myproxy_manual_login'))
				$user->resetMyproxyExpireDate();
			
	    $this->setError('Авторизация не пройдена. Уточните параметры в <a href="'.href('profile/edit#/temporary-cert').'">профиле</a>, или введите заново параметры вручную.');
			return FALSE;
		}
			
		$this->log(Lng::get('Task.model.myproxy-success-proceed'));
		
		// if(!$this->getField('is_test')){
		//$taskdir = FS_ROOT.'files/user_tasks/'.USER_AUTH_ID.'/'.$this->id.'/';
		$taskdir = $this->getTaskDir();
		
		$command  = ''
			." cd ".$taskdir. " && "
			.$env . " X509_USER_PROXY=".$tmpfile." "
			.$ngsub . " -d2 -o /home/apache/.ngjobs"
			." -e ". escapeshellarg(stripslashes($ngjob))." 2>&1";

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
	
	public function getResults($data){
		
		$user = CurUser::get();
		$output = '';
		// $debug = TRUE;
		$debug = FALSE;
		
		// если авторизационные данные тянутся из БД
		if(empty($data)){
			if($user->getField('myproxy_manual_login') || $user->getField('myproxy_expire_date') < time()){
				$user->resetMyproxyExpireDate();
				$this->setError('Требуется логин и пароль myproxy');
				return FALSE;
			}
			
			$serverId = $user->getField('myproxy_server_id');
			$login    = $user->getField('myproxy_login');
			$password = $user->getFieldPrepared('myproxy_password');
			$plifetm  = $user->getField('myproxy_expire_date') - time();
		}
		// если авторазизационные данные пользователь ввел вручную
		else{
			$serverId = (int)$data['serverId'];
			$login    = $data['login'];
			$password = $data['password'];
			$plifetm  = (int)$data['lifetime'];
		}
		
		try{
			$server = MyproxyServer::load($serverId)->getAllFields();
		}catch(Exception $e){
			$this->setError('myproxy сервер не найден');
			return FALSE;
		}
		
		$tmpfile = tempnam("/tmp", "x509_mp_");
		
		$ngjob = $this->getNgjobStr();

		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		$myProxyIsLogged = myproxy_logon($server['url'], $server['port'], $login, $password, $plifetm, $tmpfile, $debug);
		
		if(!$myProxyIsLogged){
			
			if(!$user->getField('myproxy_manual_login'))
				$user->resetMyproxyExpireDate();
			
			$this->setError('Авторизация не пройдена. Уточните параметры в <a href="'.href('profile/edit#/temporary-cert').'">профиле</a>, или введите вручную.');
			return FALSE;
		}
			
		$this->log('Запрос Майпрокси удачно! Продолжаем:');
		
		$taskdir = $this->getTaskDir();
		$env = "/bin/env";
		$ngget = "/opt/nordugrid-8.1/bin/ngget";
		
		$command  = ''
			." cd ".$taskdir. " && "
			.$env . " X509_USER_PROXY=".$tmpfile." "
			.$ngget . " -d2 ".escapeshellarg($this->getField('jobid'))." 2>&1";

		$this->log("Запуск ngget: $command ...");
		
		// выполнение команды ngsub
		exec($command, $outputArr, $retval);
		
		$response = implode("\n", $outputArr);
		$this->log($response);
		
		if($retval == 0){
		
			$this->log("Задача успешно запущена!");
			
			$jobid = preg_match('/(gsiftp:\/\/\S+)/', $response, $matches) ? $matches[1] : null;
			if(!empty($jobid)){
				$this->setField('jobid', $jobid);
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
	
	public function stop($data){
		
		
		$user = CurUser::get();
		$output = '';
		// $debug = TRUE;
		$debug = FALSE;
		
		// если авторизационные данные тянутся из БД
		if(empty($data)){
			if($user->getField('myproxy_manual_login') || $user->getField('myproxy_expire_date') < time()){
				$user->resetMyproxyExpireDate();
				$this->setError('Требуется логин и пароль myproxy');
				return FALSE;
			}
			
			$serverId = $user->getField('myproxy_server_id');
			$login    = $user->getField('myproxy_login');
			$password = $user->getFieldPrepared('myproxy_password');
			$plifetm  = $user->getField('myproxy_expire_date') - time();
		}
		// если авторазизационные данные пользователь ввел вручную
		else{
			$serverId = (int)$data['serverId'];
			$login    = $data['login'];
			$password = $data['password'];
			$plifetm  = (int)$data['lifetime'];
		}
		
		try{
			$server = MyproxyServer::load($serverId)->getAllFields();
		}catch(Exception $e){
			$this->setError('myproxy сервер не найден');
			return FALSE;
		}
		
		$tmpfile = tempnam("/tmp", "x509_mp_");

		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		$myProxyIsLogged = myproxy_logon($server['url'], $server['port'], $login, $password, $plifetm, $tmpfile, $debug);
		
		if(!$myProxyIsLogged){
			
			if(!$user->getField('myproxy_manual_login'))
				$user->resetMyproxyExpireDate();
			
			$this->setError('Авторизация не пройдена. Уточните параметры в <a href="'.href('profile/edit#/temporary-cert').'">профиле</a>, или введите вручную.');
			return FALSE;
		}
			
		$this->log('Запрос Майпрокси удачно! Продолжаем:');
		
		// if(!$this->getField('is_test')){
		//$taskdir = FS_ROOT.'files/user_tasks/'.USER_AUTH_ID.'/'.$this->id.'/';
		$taskdir = $this->getTaskDir();
		
		$env = "/bin/env";
		$ngkill = "/opt/nordugrid-8.1/bin/ngkill";
		
		$command  = ''
			.$env . " X509_USER_PROXY=".$tmpfile." "
			.$ngkill . " -d2 ".escapeshellarg($this->getField('jobid'))." 2>&1";
		
		$this->log("Запуск ngkill: $command ...");
		
		// выполнение команды ngsub
		exec($command, $outputArr, $retval);
		
		$response = implode("\n", $outputArr);
		$this->log($response);
		
		if($retval == 0){
		
			$this->log("Задача успешно остановлена!");
				
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
		
	//	return "&(executable=/bin/sleep)(arguments=1000)(jobname='GJSWI sleep test')";
		
		$ngjob = "&\n";
		foreach(unserialize($this->getField('xrsl_command')) as $k => $v)
			$ngjob .= '('.$k.'='.$v.")\n";
		
		return $ngjob;
	}

	public function getTaskDir(){
		
		return CurUser::get()->getTasksDir().$this->id.'/';
	}
	
}

class TaskCollection extends GenericObjectCollection{
	
	protected $_filters = array();
	
	// ТОЧКА ВХОДА В КЛАСС
	public static function load(){
			
		$instance = new TaskCollection();
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
		
		$sorter = new Sorter('s.id', 'DESC', $this->_getSortableFieldsTitles());
		$paginator = new Paginator('sql', array('t.*, s.title AS state_title', '
			FROM '.Task::TABLE.' t LEFT JOIN task_states s ON t.state=s.id '.$whereStr.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		// echo '<pre>'; print_r($data);
		foreach($data as &$row)
			$row = Task::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	public function getAll(){
		
		$data = db::get()->getAllIndexed('SELECT * FROM '.Task::TABLE, 'id');
		
		foreach($data as &$row)
			$row = Task::forceLoad($row['id'], $row)->getAllFieldsPrepared();
			
		return $data;
	}
	
	// поля, по которым возможна сортировка коллекции
	// каждый ключ должен быть корректным выражением для SQL ORDER BY
	protected function _getSortableFieldsTitles(){
		return array(
			'id' => array('t.id _DIR_',                     Lng::get('tasklist.id')),
			'uid' => 'uid',
			'name' => array('t.name _DIR_',                 Lng::get('tasklist.name')),
			'xrsl_command' => array('t.xrsl_command _DIR_', Lng::get('tasklist.xrsl_command')),
			'date' => array('t.date _DIR_',                 Lng::get('tasklist.date')),
			'state_title' => array('s.title _DIR_',         Lng::get('tasklist.statetitle')),
		);
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
	
}

?>