<?php

class TaskSubmit extends GenericObject{
	
	const TABLE = 'task_submits';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';

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
	
		$data['status_str'] = $data['status'] ? TaskStatus::get()->statuses[$data['status']]['title'] : 'task.state.undefined';
		$data['start_date_str'] = YDate::loadTimestamp($data['start_date'])->getStrDateShortTime();
		$data['finish_date_str'] = YDate::loadTimestamp($data['finish_date'])->getStrDateShortTime();
		
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
	
	public function submit($myproxyAuth, $preferedServer){
		
		$debug = 0;
		$tmpfile = tempnam("/tmp", "x509_mp_");
		
		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		$myProxyIsLogged = myproxy_logon(
			$myproxyAuth['url'],
			$myproxyAuth['port'],
			$myproxyAuth['login'],
			$myproxyAuth['password'],
			$myproxyAuth['lifetime'],
			$tmpfile,
			$debug
		);
		
		if(!$myProxyIsLogged){
			
			$user = CurUser::get();
			if(!$user->getField('myproxy_manual_login'))
				$user->resetMyproxyExpireDate();
			
			$this->setError('Авторизация не пройдена. Уточните параметры в <a href="'.href('profile/edit#/temporary-cert').'">профиле</a>, или введите заново параметры вручную.');
			return FALSE;
		}
			
		$this->log(Lng::get('Task.model.myproxy-success-proceed'));
		
		$env = "/bin/env";
		$ngsub = "/opt/nordugrid-8.1/bin/ngsub";
		$taskdir = $this->getFilesDir().'src/';
		$ngjob = $this->getNgjobStr();
		
		$command  = ''
			." cd ".$taskdir. " && "
			.$env . " X509_USER_PROXY=".$tmpfile." "
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
	
	public function stop($myproxyAuth){
		
		// получение данных сервера myproxy
		try {
			$myproxyServer = MyproxyServer::load($myproxyAuth['serverId'])->getAllFields();
		} catch (Exception $e) {
			$this->setError(Lng::get('Task.model.myproxy-server-not-faund'));
			return FALSE;
		}
		
		$debug = 0;
		$tmpfile = tempnam("/tmp", "x509_mp_");
		
		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		$myProxyIsLogged = myproxy_logon(
			$myproxyServer['url'],
			$myproxyServer['port'],
			$myproxyAuth['login'],
			$myproxyAuth['password'],
			$myproxyAuth['lifetime'],
			$tmpfile,
			$debug
		);
		
		if(!$myProxyIsLogged){
			
			$user = CurUser::get();
			if(!$user->getField('myproxy_manual_login'))
				$user->resetMyproxyExpireDate();
			
			$this->setError('Авторизация не пройдена. Уточните параметры в <a href="'.href('profile/edit#/temporary-cert').'">профиле</a>, или введите заново параметры вручную.');
			return FALSE;
		}
			
		$this->log(Lng::get('Task.model.myproxy-success-proceed'));
		
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
			
			$this->destroy();
			$this->log("Задача успешно остановлена!");
				
			return TRUE;
			
		}else{
			$this->setError('Скрипт вернул код ошибки: '.$retval);
			return FALSE;
		}
	}
	
	public function getResults($myproxyAuth){
		
		// получение данных сервера myproxy
		try {
			$myproxyServer = MyproxyServer::load($myproxyAuth['serverId'])->getAllFields();
		} catch (Exception $e) {
			$this->setError(Lng::get('Task.model.myproxy-server-not-faund'));
			return FALSE;
		}
		
		$debug = 0;
		$tmpfile = tempnam("/tmp", "x509_mp_");

		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		$myProxyIsLogged = myproxy_logon(
			$myproxyServer['url'],
			$myproxyServer['port'],
			$myproxyAuth['login'],
			$myproxyAuth['password'],
			$myproxyAuth['lifetime'],
			$tmpfile,
			$debug
		);
		
		if(!$myProxyIsLogged){
			
			$user = CurUser::get();
			if(!$user->getField('myproxy_manual_login'))
				$user->resetMyproxyExpireDate();
			
			$this->setError('Авторизация не пройдена. Уточните параметры в <a href="'.href('profile/edit#/temporary-cert').'">профиле</a>, или введите заново параметры вручную.');
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
			.$env . " X509_USER_PROXY=".$tmpfile." "
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
	
	public function getUid(){
		
		if (empty($this->_uid))
			throw new Exception('uid not specified');
			
		return $this->_uid;
	}
	
	public function dbGetRow(){
		
		$data = db::get()->getRow(
			"SELECT sub.*, s.uid, s.name FROM ".self::TABLE." sub
			JOIN ".TaskSet::TABLE." s ON s.id=sub.set_id
			WHERE sub.id='".$this->id."'");
		
		if (!empty($data)) {
			$this->_uid = $data['uid'];
			unset($data['uid']);
		}
		
		return $data;
	}
	
}

class TaskSubmitCollection extends GenericObjectCollection{
	
	protected $_filters = array();
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'set_id' => 'Набор',
		'index' => 'Порядковый номер',
		'status' => 'Сатус',
		'is_submitted' => 'Отправлена',
		'is_completed' => 'Завершена',
		'is_fetched' => 'Получена',
		'create_date' => 'Дата создания',
		'start_date' => 'Дата запуска',
		'finish_date' => 'Дата завершения',
	);
	
	
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
	public function getPaginated( $filters = array() ){
		
		$whereArr = array();
		if(!empty($filters['uid']))
			$whereArr[] = 'uid='.$filters['uid'];
		
		$whereStr = !empty($whereArr) ? ' WHERE '.implode(' AND ', $whereArr) : '';
		
		$sorter = new Sorter('s.id', 'DESC', $this->_getSortableFieldsTitles());
		$paginator = new Paginator('sql', array('t.*, s.title AS state_title',
			'FROM '.TaskSubmit::TABLE.' t LEFT JOIN task_states s ON t.state=s.id '.$whereStr.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		// echo '<pre>'; print_r($data);
		foreach($data as &$row)
			$row = Task::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	public function getFetchedSubmits(){
		
		$db = db::get();
		$data = $db->getAll('
			SELECT sub.*, s.uid, s.name FROM '.TaskSubmit::TABLE.' sub
			JOIN '.TaskSet::TABLE.' s ON s.id=sub.set_id
			WHERE sub.is_fetched=true '.(!empty($this->filters['uid']) ? ' AND s.uid='.$this->filters['uid'] : '').'
			ORDER BY `index`');
		
		return $data;
	}
	
	public function getTasksBySet($set_id){
		
		$data = db::get()->getAll('SELECT * FROM '.TaskSubmit::TABLE.' WHERE set_id='.$set_id.' ORDER BY `index` DESC');
		
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

}

?>