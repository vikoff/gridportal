<?php

class TaskSetController extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'TaskSet/';
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = 'list';
	protected $_defaultBackendDisplay = 'list';
	
	// права на выполнение методов контроллера
	public $permissions = array(
		'display_list' 			=> PERMS_REG,
		'display_view' 			=> PERMS_REG,
		'display_new'			=> PERMS_REG,
		'display_customize' 	=> PERMS_REG,
		'display_submit'		=> PERMS_REG,
		'display_edit_file'		=> PERMS_REG,
		'display_file_constructor' => PERMS_REG,
		'display_delete'		=> PERMS_REG,
		'display_test'			=> PERMS_REG,
		
		'admin_display_list'	=> PERMS_ADMIN,
		'admin_display_edit'	=> PERMS_ADMIN,
		'admin_display_copy'	=> PERMS_ADMIN,

		'action_create' 		=> PERMS_REG,
		'action_save' 			=> PERMS_REG,
		'action_delete' 		=> PERMS_REG,
		'action_upload_file'	=> PERMS_REG,
		'action_save_file'		=> PERMS_REG,
		'action_save_constructor' => PERMS_REG,
		'action_submit'			=> PERMS_REG,
		
		'ajax_get_task_files'	=> PERMS_REG,
		'ajax_delete_task_file'	=> PERMS_REG,
	);
	
	protected $_title = null;
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY LIST */
	public function display_list($params = array()){
		
		$collection = new TaskSetCollection(array('uid' => USER_AUTH_ID));
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		FrontendViewer::get()
			->setTitle('Коллекция')
			->setLinkTags($collection->getLinkTags())
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'list.php', $variables)
			->render();
	}
	
	/** DISPLAY VIEW */
	public function display_view($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		
		$variables = array_merge(TaskSet::Load($instanceId)->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'submits' => TaskSubmitCollection::load()->getTasksBySet($instanceId),
		));
		
		// echo '<pre>'; print_r(TaskSubmitCollection::load()->getTasksBySet($instanceId)); die;
		FrontendViewer::get()
			->setTitle('Детально')
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'view.php', $variables)
			->render();
	}
	
	/** DISPLAY NEW */
	public function display_new($params = array()){
		
		$projectId = getVar($params[0], 0, 'int');
		$allowedProjects = CurUser::get()->getAllowedProjects();
		
		// если проект недоступен пользователю
		if (!isset( $allowedProjects[$projectId] ))
			throw new Exception('Вы не можете создавать задачи в этом проекте.');
		
		$projectInstance = Project::load($projectId);
		
		$pageTitle = 'Создание новой задачи';
		
		$variables = array_merge($_POST, array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'projectId' => $projectId,
			'projectName' => $projectInstance->getField('name'),
			'profileList' => TaskProfileCollection::load(array('project_id' => $projectId))->getAll(),
		));
		
		FrontendViewer::get()
			->prependTitle($pageTitle)
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'create.php', $variables)
			->render();
	}
	
	public function display_customize($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		$instance = TaskSet::Load($instanceId);
		
		$pageTitle = 'Редактирование параметров задачи';
		
		$variables = array(
			'instanceId' => $instanceId,
		);
		
		FrontendViewer::get()
			->prependTitle($pageTitle)
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'customize.php', $variables)
			->render();
	}
	
	public function display_test($params = array()){
		
		$set = TaskSet::load(21);
		$multipliers = array();
		
		// поиск всех множителей в файле
		foreach($set->getAllFilesList() as $f) {
			if ( $ftype = TaskSet::getFileType($f) ) {
				$multipliers = array_merge(
					$multipliers,
					TaskSet::getFileConstructor($ftype, $set->getValidFileName($f))->getMultipliers()
				);
			}
		}
		
		// создание обработчика множителей
		$submitter = new BatchSubmitter();
		foreach($multipliers as $mult)
			$submitter->addMultiplier($mult['file'], $mult['row'], $mult['values'], $mult['valuesStr']);
		
		// создание комбинации
		echo '<pre>';
		while($combination = $submitter->getNextCombination()) {
			print_r($combination);
			continue;
			foreach($set->getAllFilesList() as $f) {
				if ( $ftype = TaskSet::getFileType($f) ) {
					$fullname = $set->getValidFileName($f);
					if (isset($combination[$fullname]))
						echo 'file '.$fullname.' with combination: <br />'
							.TaskSet::getFileConstructor($ftype, $fullname)->getCombination($combination[$fullname]);
				}
			}
		}
	}
	
	public function display_submit($params = array()){
		
		$user = CurUser::get();
		
		$instanceId = getVar($params[0], 0, 'int');
		$instance = TaskSet::Load($instanceId);
		
		$viewer = FrontendViewer::get()
			->setTitle('Запуск задачи')
			->setTopMenuActiveItem('tasks');
		
		if (!$instance->hasGridjobFile()){
			$viewer
				->setContentPhpFile(self::TPL_PATH.'xrsl_empty.php', array('id' => $instanceId))
				->render();
			return;
		}
		
		$manualMyproxyLogin = $user->getField('myproxy_manual_login') || $user->getField('myproxy_expire_date') < time();
			
			$basedir = $instance->getFilesDir().'src/';
			$numSubmits = 1;
			foreach($instance->getAllFilesList() as $f)
				if ( $ftype = TaskSet::getFileType($f) )
					if ($mults = TaskSet::getFileConstructor($ftype, $instance->getValidFileName($f))->getMultipliers())
						foreach($mults as $mult)
							$numSubmits *= count($mult['values']);
		
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'showMyproxyLogin' => $manualMyproxyLogin,
			'myproxyServersList' => $manualMyproxyLogin ? MyproxyServerCollection::load()->getAll() : array(),
			'numSubmits' => $numSubmits,
		));
		
		$viewer
			->setContentPhpFile(self::TPL_PATH.'submit.php', $variables)
			->render();
	}
	
	public function display_edit_file($params = array()){
		
		$fname = getVar($_GET['file']);
		if (empty($fname))
			die ('Файл не найден #0');
			
		$id = getVar($params[0], 0, 'int');
		try {
			$instance = TaskSet::load($id);
			
			$fullname = $instance->getValidFileName($fname);
			if (empty($fullname))
				throw new Exception('Файл не найден #1');
			
			$vars = array(
				'instanceId' => $id,
				'fname' => $fname,
				'content' => file_get_contents($fullname),
			);
			
			// if (!empty($_POST))
				// {echo '<pre>'; print_r(FrontendViewer::get()); die;}
			echo FrontendViewer::get()->getContentPhpFile(self::TPL_PATH.'edit_file.php', $vars);
			// include(FS_ROOT.'templates/'.self::TPL_PATH.'edit_file.php');
		}
		catch(Exception $e){
			echo 'Ошибка! '.$e->getMessage();
		}
		
	}
	
	public function display_file_constructor($params = array()){
		
		$fname = getVar($_GET['file']);
		if (empty($fname))
			die ('Файл не найден #0');
			
		$id = getVar($params[0], 0, 'int');
		try {
			$instance = TaskSet::load($id);
			
			$fullname = $instance->getValidFileName($fname);
			if (empty($fullname))
				throw new Exception('Файл не найден #1');
			
			$fileType = $instance->getFileType($fullname);
			if (empty($fileType))
				throw new Exception('Неизвестный тип файла');
			
			$vars = array(
				'instanceId' => $id,
				'fname' => $fname,
				'formData' => TaskSet::getFileConstructor($fileType, $fullname)->getConstructorFormData()
			);
			
			// echo '<pre>'; print_r(TaskSet::getFileConstructor($fileType, $fullname)->getConstructorFormData()); die;
			include(FS_ROOT.'templates/'.self::TPL_PATH.'file_constructor.php');
		}
		catch(Exception $e){
			echo 'Ошибка! '.$e->getMessage();
		}
		
	}
	
	/** DISPLAY DELETE (ADMIN) */
	public function display_delete($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSet::Load($instanceId);

		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
		));
		
		FrontendViewer::get()
			->prependTitle('Удаление задачи')
			->setContentPhpFile(self::TPL_PATH.'delete.php', $variables)
			->render();
	}
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	/** DISPLAY LIST (ADMIN) */
	public function admin_display_list($params = array()){
		
		$collection = new TaskSetCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendViewer::get()
			->prependTitle('Список элементов')
			->setLinkTags($collection->getLinkTags())
			->setContentPhpFile(self::TPL_PATH.'admin_list.php', $variables);
	}
	
	/** DISPLAY EDIT (ADMIN) */
	public function admin_display_edit($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSet::Load($instanceId);
		
		$pageTitle = '<span style="font-size: 14px;">Редактирование элемента</span> #'.$instance->getField('id');
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendViewer::get()
			->prependTitle('Редактирование записи')
			->setBreadcrumbs('add', array(null, 'Редактирование записи'))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
		
	}
	
	/** DISPLAY COPY (ADMIN) */
	public function admin_display_copy($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSet::Load($instanceId);
		
		$pageTitle = 'Копирование записи';
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
		
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION CREATE */
	public function action_create($params = array()){
		
		$instance = TaskSet::create();
		
		if($instance->save($_POST)){
			Messenger::get()->addSuccess(Lng::get('task.controller.RecordAddSucsess'));
			// редирект на дальнейшую инициализацию (для новых объектов)
			$this->_redirectUrl = 'task-set/customize/'.$instance->id;
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось создать задачу:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION SAVE (ADMIN) */
	public function action_save($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new TaskSet($instanceId);
		
		if($instance->save($_POST)){
			Messenger::get()->addSuccess('Запись сохранена');
			$this->_redirectUrl = !empty($this->_redirectUrl) ? preg_replace('/\(%([\w\-]+)%\)/e', '$instance->getField("$1")', $this->_redirectUrl) : null;
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить запись:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION DELETE (ADMIN) */
	public function action_delete($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskSet::Load($instanceId);
		
		// установить редирект на admin-list
		$this->setRedirectUrl('task-set/list');
	
		if($instance->destroy()){
			Messenger::get()->addSuccess('Задача удалена');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось удалить задачу:', $instance->getError());
			// выполнить редирект принудительно
			$this->forceRedirect();
			return FALSE;
		}

	}
	
	/** ACTION UPLOAD-FILE */
	public function action_upload_file($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskSet::Load($instanceId);
		
		if($instance->getField('uid') != USER_AUTH_ID){
			echo 'Указанная задача не пренадлежит вам';
			return FALSE;
		}
		
		if(empty($_FILES)){
			echo 'Файл не передан';
			return FALSE;
		}

		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = $instance->getFilesDir().'src/';
		
		if(!is_dir($targetPath))
			mkdir($targetPath, 0777, TRUE);
		
		$targetName = $_FILES['Filedata']['name'];
		$targetFullName =  $targetPath.$targetName;
		move_uploaded_file($tempFile, $targetFullName);
		
		// если был загружен файл nordujob
		if(!$instance->hasGridjobFile() && $targetName == 'nordujob')
			$instance->hasGridjobFile(true);
			
		return TRUE;
	}
	
	public function action_save_file($params = array()){
		
		$fname = getVar($_GET['file']);
		if (empty($fname))
			throw new Exception('Файл не найден #0');
			
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskSet::load($instanceId);
		
		if($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception("Указанная задача не пренадлежит вам");
		
		$fullname = $instance->getValidFileName($fname);
		if (empty($fullname))
			throw new Exception('Файл не найден #1');
		
		$content = str_replace("\r\n", "\n", Tools::unescape($_POST['content']));
		file_put_contents($fullname, $content);
		FrontendViewer::get()->setVariables(array('saved_success' => true));
		return TRUE;
	}

	public function action_save_constructor($params = array()){
		
		// echo '<pre>'; print_r($_POST); echo '</pre><hr />';
		// foreach (Tools::unescape($_POST['items']) as $index => $data) // DEBUG
			// echo TaskSet::parseFormMultiplier($data['value']).'<br />'; // DEBUG
		
		// die; // DEBUG
		
		$fname = getVar($_GET['file']);
		if (empty($fname))
			throw new Exception('Файл не найден #0');
			
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskSet::load($instanceId);
		
		if($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception("Указанная задача не пренадлежит вам");
		
		$fullname = $instance->getValidFileName($fname);
		if (empty($fullname))
			throw new Exception('Файл не найден #1');
		
		$modifiedRows = Tools::unescape($_POST['items']);
		$contentArr = file($fullname);
		foreach($modifiedRows as $index => $data)
			$contentArr[$index] = $data['pre_text'].TaskSet::parseFormMultiplier($data['value']).$data['post_text']."\n";
		file_put_contents($fullname, implode('', $contentArr));
		
		return TRUE;
	}
	
	public function action_submit($params = array()){
		
		// echo '<pre>'; print_r($_POST); die;
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskSet::load($instanceId);
		
		// получение авторизационных данных myproxy
		try {
			$myProxyAuthData = !empty($_POST['myproxy-autologin'])
				? CurUser::get()->getMyproxyLoginData()
				: array(
					'serverId' => (int)getVar($_POST['server']),
					'login' => getVar($_POST['user']['name']),
					'password' => getVar($_POST['user']['pass']),
					'lifetime' => (int)getVar($_POST['lifetime']),
				);
			$_myproxyServer = MyproxyServer::load($myProxyAuthData['serverId'])->getAllFields();
			$myProxyAuthData['url'] = $_myproxyServer['url'];
			$myProxyAuthData['port'] = $_myproxyServer['port'];
		} catch (Exception $e) {
			Messenger::get()->addError(Lng::get('task.warnings'), $e->getMessage());
			return FALSE;
		}
			
		if($report = $instance->submit($myProxyAuthData, getVar($_POST['prefer-server']))){
		
			App::stopDisplay();
			
			Messenger::get()->addSuccess(Lng::get('Task.controller.task-run-success'));
			if($instance->hasError())
				Messenger::get()->addError(Lng::get('task.warnings'), $instance->getError());
				
			$this->snippet_submit_complete($instance, $report['queue_length']);
			return TRUE;
		}
		else{
			Messenger::get()->addError(
				Lng::get('Task.controller.task-run-fail'),
				$instance->firstSubmit->getError().'<h3>Лог</h3>'.$instance->firstSubmit->getLog()
			);
			return FALSE;
		}
	}
	
	//////////////////////
	////// SNIPPETS //////
	//////////////////////
	
	public function snippet_submit_complete(TaskSet $taskModel, $queueLength){
		
		$variables = array(
			'id' => $taskModel->id,
			'numSubmits' => count($taskModel->submits),
			'queueLength' => $queueLength,
			'log' => $taskModel->firstSubmit->getLog(),
		);
		
		FrontendViewer::get()
			->setTitle(Lng::get('Task.controller.task-run-success'))
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'submit_complete.php', $variables)
			->render();
	}
	
	//////////////////
	////// AJAX //////
	//////////////////
	
	/** AJAX GET-TASK-FILES */
	public function ajax_get_task_files($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0, 'int');
			$instance = TaskSet::Load($instanceId);
			
			if($instance->getField('uid') != USER_AUTH_ID){
				echo '{"error": "Указанная задача не пренадлежит вам"}';
				return FALSE;
			}
			
			$elms = $instance->getAllFilesList();
			foreach($elms as &$elm)
				$elm = '{"name": "'.$elm.'", "type":"'.(string)TaskSet::getFileType($elm).'"}';
			
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
		$instance = TaskSet::Load($instanceId);
		
		if($instance->getField('uid') != USER_AUTH_ID){
			echo 'Указанная задача не пренадлежит вам';
			return FALSE;
		}
		
		$taskDir = $instance->getFilesDir().'src/';
		
		$fileName = getVar($_POST['file'], '');
		$fullName = $taskDir.$fileName;
		if(!is_dir($taskDir) || empty($fileName) || !file_exists($fullName)){
			echo 'Файл "'.$fullName.'" не найден';
			return FALSE;
		}
		
		unlink($fullName);
		
		// если был удален файл nordujob
		if($fileName == 'nordujob' && $instance->hasGridjobFile())
			$instance->hasGridjobFile(false);
			
		echo 'ok';
		return TRUE;
	}
	

}

?>