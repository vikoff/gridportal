<?php

class TaskSubmit extends GenericObject{
	
	const TABLE = 'task_submits';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';

	private $_log = array();
	
	private $_setInstance = null;
	
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
	
	/** СЛУЖЕБНЫЙ МЕТОД (получение констант из родителя) */
	public function getConst($name){
		return constant(__CLASS__.'::'.$name);
	}
	
	public function setSetInstance(TaskSet $set){
		$this->_setInstance = $set;
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
                'is_submitted' => array('settype' => 'bool'),
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
		
		// $data['author'] = USER_AUTH_ID;
		// $data['modif_date'] = time();
		// if($this->isNewObj)
			// $data['create_date'] = time();
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ */
	public function afterSave($data){
		
		$dir = $this->getFilesDir();
		if (!is_dir($dir))
			mkdir($dir, 0777, TRUE);
	}
	
	/** ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА */
	public function beforeDestroy(){
	
		$filesDir = $this->getFilesDir();
		`rm -rf $filesDir`;
	}
	
	public function submit($myproxyServer, $myProxyAuth, $preferedServer){
		
		$debug = 0;
		
		$tmpfile = tempnam("/tmp", "x509_mp_");
		$env = "/bin/env";
		$ngsub = "/opt/nordugrid-8.1/bin/ngsub";
		
		$ngjob = $this->getNgjobStr();

		require_once(FS_ROOT.'includes/myproxy/myproxyClient.php');
		$myProxyIsLogged = myproxy_logon(
			$myproxyServer['url'],
			$myproxyServer['port'],
			$myProxyAuth['login'],
			$myProxyAuth['password'],
			$myProxyAuth['lifetime'],
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
		
		$taskdir = $this->getFilesDir();
		
		$command  = ''
			." cd ".$taskdir. " && "
			.$env . " X509_USER_PROXY=".$tmpfile." "
			.$ngsub . " -d2 -o /home/apache/.ngjobs"
			.(!empty($preferedServer) ? ' -c '.escapeshellarg($preferedServer) : '')
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
				$this->setField('is_submitted', TRUE);
				$this->setField('start_date', time());
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
	
	public function log($msg){
	
		$this->_log[] = $msg;
	}
	
	public function getLog(){
	
		return implode('<br />', $this->_log);
	}
	
	public function getNgjobStr(){
		
		return file_get_contents($this->getFilesDir().'nordujob');
		
	//	return "&(executable=/bin/sleep)(arguments=1000)(jobname='GJSWI sleep test')";
		
		// $ngjob = "&\n";
		// foreach(unserialize($this->getField('xrsl_command')) as $k => $v)
			// $ngjob .= '('.$k.'='.$v.")\n";
		
		// return $ngjob;
	}

	public function getFilesDir(){
		
		return FS_ROOT.'files/users/'.$this->_setInstance->getField('uid').'/task_sets/'.$this->getField('set_id').'/submits/'.$this->id.'/';
	}

}

class TaskSubmitCollection extends GenericObjectCollection{
	
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
		'start_date' => 'Дата запуска',
		'finish_date' => 'Дата завершения',
	);
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function Load(){
			
		$instance = new TaskSubmitCollection();
		return $instance;
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.TaskSubmit::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = TaskSubmit::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	public function getTasksBySet($set_id){
		
		$data = db::get()->getAll('SELECT * FROM '.TaskSubmit::TABLE.' WHERE set_id='.$set_id);
		
		foreach($data as &$row)
			$row = TaskSubmit::forceLoad($row['id'], $row)->getAllFieldsPrepared();
			
		return $data;
	}

}

?>