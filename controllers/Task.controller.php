<?php

class TaskController extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'Task/';
      
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = 'list';
	protected $_defaultBackendDisplay = 'list';
	
	// права на выполнение методов контроллера
	public $permissions = array(
		'display_list' 			=> PERMS_REG,
		'display_view' 			=> PERMS_REG,
		'display_new'			=> PERMS_REG,
		'display_xrsl_edit'		=> PERMS_REG,
		'display_upload_files'	=> PERMS_REG,
		'display_edit'			=> PERMS_REG,
		'display_get_results'	=> PERMS_REG,
		'display_stop'			=> PERMS_REG,
		'display_delete'		=> PERMS_REG,
		'display_analyze'		=> PERMS_REG,
		
		'admin_display_list'	=> PERMS_ADMIN,

		'action_create' 		=> PERMS_REG,
		'action_save' 			=> PERMS_REG,
		'action_rename' 		=> PERMS_REG,
		'action_stop' 			=> PERMS_REG,
		'action_delete' 		=> PERMS_REG,
		'action_run'			=> PERMS_REG,
		'action_get_results'	=> PERMS_REG,
		'action_upload_file'	=> PERMS_REG,
		
		'ajax_get_task_files'	=> PERMS_REG,
		'ajax_delete_task_file'	=> PERMS_REG,
	);
	
	public function init(){
		
		FrontendViewer::get()->setTopMenuActiveItem('tasks');
	}
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	// DISPLAY LIST
	public function display_list($params = array()){
	
		$collection = new TaskCollection(array('uid' => USER_AUTH_ID));
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		FrontendViewer::get()
			->setTitle('Список задач пользователя')
			->setLinkTags($collection->getLinkTags())
			->setTopMenuActiveItem('tasks')
			->setContentSmarty(self::TPL_PATH.'list.tpl', $variables)
			->render();
	}
	
	// DISPLAY VIEW
	public function display_view($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		
		try{
			$ins = Task::Load($instanceId);
			// echo '<pre>'; print_r($ins->GetAllFieldsPrepared()); die;
			$variables = Task::Load($instanceId)->GetAllFieldsPrepared();
			FrontendViewer::get()
				->setTitle('Детально')
				->setContentSmarty(self::TPL_PATH.'view.tpl', $variables)
				->render();
		}
		catch(Exception $e){
			FrontendViewer::get()->error404('Запрашиваемая страница не найдена (#'.__LINE__.')');
		}
	}
	
	// DISPLAY NEW
	public function display_new($params = array()){
		
		$projectId = getVar($params[0], 0, 'int');
		$allowedProjects = CurUser::get()->getAllowedProjects();
		
		// если проект недоступен пользователю
		if (!isset( $allowedProjects[$projectId] ))
			throw new Exception('Вы не можете создавать задачи в этом проекте.');
		
		$projectInstance = Project::load($projectId);
		
		$variables = array_merge($_POST, array(
			'instanceId' => 0,
			'projectId' => $projectId,
			'projectName' => $projectInstance->getField('name'),
		));
		
		FrontendViewer::get()
			->prependTitle('создание новой записи')
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'create.php', $variables)
			->render();
	}
	
	// DISPLAY EDIT
	public function display_edit($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = Task::Load($instanceId);
		
			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
				'redirect' => getVar($_POST['redirect']),
				'validation' => $instance->getValidator()->getJsRules(),
			));
			
			FrontendViewer::get()
				->prependTitle('редактирование записи')
				->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables)
				->render();
		}
		catch(Exception $e){
			FrontendViewer::get()->error404($e->getMessage());
		}
		
	}
	
	public function display_get_results($params = array()){
	
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Task::Load($instanceId);
		$user = CurUser::get();
		
		$manualMyproxyLogin = $user->getField('myproxy_manual_login') || $user->getField('myproxy_expire_date') < time();

		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'showMyproxyLogin' => $manualMyproxyLogin,
			'myproxyServersList' => $manualMyproxyLogin ? MyproxyServerCollection::load()->getAll() : array(),
		));
		
		FrontendViewer::get()
			->prependTitle('Сохранение результата задачи')
			->setContentPhpFile(self::TPL_PATH.'get_result.php', $variables)
			->render();
	}
	
	public function display_stop($params = array()){
	
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Task::Load($instanceId);
		$user = CurUser::get();
		
		$manualMyproxyLogin = $user->getField('myproxy_manual_login') || $user->getField('myproxy_expire_date') < time();

		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'showMyproxyLogin' => $manualMyproxyLogin,
			'myproxyServersList' => $manualMyproxyLogin ? MyproxyServerCollection::load()->getAll() : array(),
		));
		
		FrontendViewer::get()
			->prependTitle('Остановка задачи')
			->setContentPhpFile(self::TPL_PATH.'stop.php', $variables)
			->render();
	}
	
	// DISPLAY DELETE
	public function display_delete($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = Task::Load($instanceId);

			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
			));
			
			FrontendViewer::get()
				->prependTitle('Удаление задачи')
				->setContentSmarty(self::TPL_PATH.'delete.tpl', $variables)
				->render();
		}
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
		}
		
	}
	
	// DIAPLAY XRSL EDIT
	public function display_xrsl_edit($params){
		
		
		$instanceId = getVar($params[0], 0, 'int');
		$user = CurUser::get();
		
		try{
			$instance = Task::Load($instanceId);
			
			$hasGridjobFile = $instance->hasGridjobFile();
			$manualMyproxyLogin = $user->getField('myproxy_manual_login') || $user->getField('myproxy_expire_date') < time();
			
			$variables = array_merge(Task::Load($instanceId)->GetAllFieldsPrepared(), array(
				'gridjobfile' => $instance->parseGridJobFile(),
				'showMyproxyLogin' => $manualMyproxyLogin,
				'myproxyServersList' => $manualMyproxyLogin ? MyproxyServerCollection::load()->getAll() : array(),
			));
			
			FrontendViewer::get()
				->setTitle('Загрузка файлов')
				->setContentSmarty(self::TPL_PATH.($hasGridjobFile ? 'xrsl_edit.tpl' : 'xrsl_empty.tpl'), $variables)
				->render();
		}
		catch(Exception $e){
			FrontendViewer::get()->error404('Запрашиваемая страница не найдена (#'.__LINE__.')');
		}
	}
	
	// DISPLAY UPLOAD FILES
	public function display_upload_files($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		
		try{
			$variables = array_merge(Task::Load($instanceId)->GetAllFieldsPrepared(), array(
				'session_id' => session_id(),
			));
			
			FrontendViewer::get()
				->setTitle('Загрузка файлов')
				->setContentSmarty(self::TPL_PATH.'upload_files.tpl', $variables)
				->render();
		}
		catch(Exception $e){
			FrontendViewer::get()->error404('Запрашиваемая страница не найдена (#'.__LINE__.')');
		}
	}
	
	public function display_analyze($params = array()){
		
		$path = getVar($_GET['path']);
		$collection = TaskCollection::load(array('uid' => USER_AUTH_ID));
		
		if(isset($_GET['act'])){
			switch($_GET['act']){
				case 'download-dir':
					$collection->downloadDir($path);
					exit;
			}
		}
		
		$fileTree = $collection->getFileTree($path);
		
		$variables=array(
			'fileTree' => $fileTree,
		);
		
		FrontendViewer::get()
			->setTitle(Lng::get('task.analyze'))
			->setTopMenuActiveItem('analyze')
	        ->setContentPhpFile(self::TPL_PATH.'analyze.php', $variables)
			->render();
		
	}
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	// DISPLAY LIST (ADMIN)
	public function admin_display_list($params = array()){
		
		$collection = new TaskCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendViewer::get()
			->prependTitle('коллекция')
			->setLinkTags($collection->getLinkTags())
			->setContentSmarty(self::TPL_PATH.'admin_list.tpl', $variables);
	}
	
	////////////////////////
	////// SYS DISPLAY//////
	////////////////////////
	
	public function sysdisplay_task_submit_complete(Task $taskModel){
		
		$variables = array(
			'id' => $taskModel->id,
			'log' => $taskModel->getLog(),
		);
		
		FrontendViewer::get()
			->setTitle(Lng::get('Task.controller.task-run-success'))
			->setContentPhpFile(self::TPL_PATH.'task_submit_complete.php', $variables)
			->render();
	}
	
	
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION CREATE */
	public function action_create($params = array()){
		
		$instance = Task::create();
		
		if($instance->save($_POST)){
			Messenger::get()->addSuccess(Lng::get('task.controller.RecordAddSucsess'));
			// редирект на дальнейшую инициализацию (для новых объектов)
			$this->_redirectUrl = 'task/upload-files/'.$instance->id;
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить запись:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION SAVE */
	public function action_save($params = array()){
	
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new Task($instanceId);
		
		if($instance->Save($_POST)){
			Messenger::get()->addSuccess(Lng::get('task.controller.RecordAddSucsess'));
			if($instanceId) // редирект на список (для существующих объектов)
				$this->_redirectUrl = 'task';
			else			// редирект на дальнейшую инициализацию (для новых объектов)
				$this->_redirectUrl = 'task/'.($instance->getField('is_test') ? 'xrsl-edit' : 'upload-files').'/'.$instance->id;
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить запись:', $instance->getError());
			return FALSE;
		}
	}
	
	public function action_rename($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new Task($instanceId);
		
		if($instance->rename(getVar($_POST['name']))){
			Messenger::get()->addSuccess(Lng::get('task.controller.RecordAddSucsess'));
			$this->_redirectUrl = 'task';
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить запись:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION UPLOAD-FILE */
	public function action_upload_file($params = array()){
		
		// App::stopDisplay();
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = Task::Load($instanceId);
		
		if($instance->getField('uid') != USER_AUTH_ID){
			echo 'Указанная задача не пренадлежит вам';
			return FALSE;
		}
		
		if(empty($_FILES)){
			echo 'Файл не передан';
			return FALSE;
		}

		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = $instance->getTaskDir().'src/';
		
		if(!is_dir($targetPath))
			mkdir($targetPath, 0777, TRUE);
		
		$targetName = $_FILES['Filedata']['name'];
		$targetFullName =  $targetPath.$targetName;
		move_uploaded_file($tempFile, $targetFullName);
		
		// если был загружен файл nordujob
		if(!$instance->getField('is_gridjob_loaded') && $targetName == 'nordujob')
			$instance->hasGridjobFile(true);
			
		return TRUE;
	}
	
	/** ACTION RUN */
	public function action_run($params = array()){
		
		// echo '<pre>'; print_r($_POST); die;
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = Task::load($instanceId);
		
		
        // сохранение xrsl
        if(!$instance->xrsl_save($_POST['xrsl'])){
			Messenger::get()->addError('Параметры заданы неверно:', $instance->getError());
			return FALSE;
		}
		
		// myproxy-autologin
		
		// запуск задачи
		$data = !empty($_POST['myproxy-autologin'])
			? FALSE
			: array(
				'serverId' => getVar($_POST['server']),
				'login' => getVar($_POST['user']['name']),
				'password' => getVar($_POST['user']['pass']),
				'lifetime' => getVar($_POST['lifetime']),
			);
			
		if($instance->run($data)){
		
			App::stopDisplay();
			
			Messenger::get()->addSuccess(Lng::get('Task.controller.task-run-success'));
			if($instance->hasError())
				Messenger::get()->addError(Lng::get('task.warnings'), $instance->getError());
				
			$this->sysdisplay_task_submit_complete($instance);
			return TRUE;
		}
		else{
			Messenger::get()->addError(Lng::get('Task.controller.task-run-fail'), $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION STOP */
	public function action_get_results($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = Task::Load($instanceId);
		
		$data = !empty($_POST['myproxy-autologin'])
			? FALSE
			: array(
				'serverId' => getVar($_POST['server']),
				'login' => getVar($_POST['user']['name']),
				'password' => getVar($_POST['user']['pass']),
				'lifetime' => getVar($_POST['lifetime']),
			);
	
		if($instance->getResults($data)){
			Messenger::get()->addSuccess(Lng::get('xrls_edit.success'));
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось получить задачу:', $instance->getError());
			// выполнить редирект принудительно
			$this->forceRedirect();
			return FALSE;
		}
	}
	
	/** ACTION STOP */
	public function action_stop($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = Task::Load($instanceId);
		
		$data = !empty($_POST['myproxy-autologin'])
			? FALSE
			: array(
				'serverId' => getVar($_POST['server']),
				'login' => getVar($_POST['user']['name']),
				'password' => getVar($_POST['user']['pass']),
				'lifetime' => getVar($_POST['lifetime']),
			);
	
		if($instance->stop($data)){
			Messenger::get()->addSuccess('Задача остановлена');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось остановить задачу:', $instance->getError());
			// выполнить редирект принудительно
			$this->forceRedirect();
			return FALSE;
		}
	}
	
	/** ACTION DELETE */
	public function action_delete($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = Task::Load($instanceId);
		
		// установить редирект на admin-list
		$this->setRedirectUrl('task/list');
	
		if($instance->Destroy()){
			Messenger::get()->addSuccess(Lng::get('task.controller.RecordRemoveSucsess'));
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось удалить запись:', $instance->getError());
			// выполнить редирект принудительно
			$this->forceRedirect();
			return FALSE;
		}

	}
	

	////////////////////
	////// AJAX   //////
	////////////////////
	
	/** AJAX GET-TASK-FILES */
	public function ajax_get_task_files($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0, 'int');
			$instance = Task::Load($instanceId);
			
			if($instance->getField('uid') != USER_AUTH_ID){
				echo '{"error": "Указанная задача не пренадлежит вам"}';
				return FALSE;
			}
			
			$elms = $instance->getAllTaskFilesList();
			foreach($elms as &$elm)
				$elm = '"'.$elm.'"';
			
			echo '{"error": "", "data": ['.implode(',', $elms).']}';
			return TRUE;
		}
		catch(Exception $e){
			echo '{"error": "Задача не найдена"}';
			return FALSE;
		}
	}
	
	/** AJAX DELETE-TASK-FILE */
	public function ajax_delete_task_file($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		$instance = Task::Load($instanceId);
		
		if($instance->getField('uid') != USER_AUTH_ID){
			echo 'Указанная задача не пренадлежит вам';
			return FALSE;
		}
		
		$taskDir = $instance->getTaskDir().'src/';
		
		$fileName = getVar($_POST['file'], '');
		$fullName = $taskDir.$fileName;
		if(!is_dir($taskDir) || empty($fileName) || !file_exists($fullName)){
			echo 'Файл "'.$fullName.'" не найден';
			return FALSE;
		}
		
		unlink($fullName);
		
		// если был удален файл nordujob
		if($fileName == 'nordujob' && $instance->getField('is_gridjob_loaded'))
			$instance->hasGridjobFile(false);
			
		echo 'ok';
		return TRUE;
	}
	
}

?>