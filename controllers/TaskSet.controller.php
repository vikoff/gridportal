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
		'display_statistics'	=> PERMS_REG,
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
		'ajax_get_statuses'		=> PERMS_REG,
		'ajax_list'				=> PERMS_REG, /* ужасный костыль, pt #1 */
		'ajax_view'				=> PERMS_REG,
		'ajax_statistics'		=> PERMS_REG,
	);
	
	protected $_title = null;
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY LIST */
	public function display_list($params = array()){
		
		$collection = new TaskSetCollection(array('uid' => USER_AUTH_ID, 'search' => getVar($_GET['search'])));
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		FrontendViewer::get()
			->setTitle('Задачи')
			->setLinkTags($collection->getLinkTags())
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'list.php', $variables)
			->render();
	}
	
	/** DISPLAY VIEW */
	public function display_view($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		
		$data = TaskSet::Load($instanceId)->GetAllFieldsPrepared();
		if ($data['uid'] != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
			
		$submits = TaskSubmitCollection::load(array('set_id' => $instanceId));
		$variables = array_merge($data, array(
			'instanceId' => $instanceId,
			'submits' => $submits->getPaginated(),
			'submitPagination' => $submits->getPagination(),
			'submitSorters' => $submits->getSortableLinks(),
		));
		
		FrontendViewer::get()
			->setTitle('Задача '.$data['name'])
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
			'projectName' => Lng::get($projectInstance->getField('name_key')),
			'profileList' => TaskProfileCollection::load(array('project_id' => $projectId))->getAll(),
		));
		
		FrontendViewer::get()
			->prependTitle($pageTitle)
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'create.php', $variables)
			->render();
	}
	
	/** DISPLAY CUSTOMIZE */
	public function display_customize($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		$instance = TaskSet::Load($instanceId);
		
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
		
		$pageTitle = 'Редактирование параметров задачи';
		
		$variables = array(
			'instanceId' => $instanceId,
			'files' => $instance->getAllFilesWithTypes(),
		);
		
		FrontendViewer::get()
			->prependTitle($pageTitle)
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'customize.php', $variables)
			->render();
	}
	
	/** DISPLAY STATISTICS */
	public function display_statistics($params = array()){
		
		// статистика конкретного сета
		if ($curSet = getVar($params[0], 0, 'int')) {
			$this->_display_set_statistics($curSet);
			exit;
		}
		
		// статистика по всем сетам
		$collection = new TaskSetCollection(array('search' => getVar($_GET['search'])));
		$variables = array(
			'collection' => $collection->getPaginated(array('withUsers' => TRUE)),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		FrontendViewer::get()
			->setTitle('Статистика')
			->setLinkTags($collection->getLinkTags())
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'statistics.php', $variables)
			->render();
	}
	
	public function _display_set_statistics($instanceId){
		
		$submits = TaskSubmitCollection::load(array('set_id' => $instanceId));
		
		$data = TaskSet::Load($instanceId)->GetAllFieldsPrepared();
		$variables = array_merge($data, array(
			'instanceId' => $instanceId,
			'submits' => $submits->getPaginated(),
			'submitPagination' => $submits->getPagination(),
			'submitSorters' => $submits->getSortableLinks(),
		));
		
		FrontendViewer::get()
			->setTitle('Задача '.$data['name'])
			->setTopMenuActiveItem('tasks')
			->setContentPhpFile(self::TPL_PATH.'set_statistics.php', $variables)
			->render();
	}
	
	/** DISPLAY SUBMIT */
	public function display_submit($params = array()){
		
		$user = CurUser::get();
		
		$instanceId = getVar($params[0], 0, 'int');
		$instance = TaskSet::Load($instanceId);
		
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
		
		$viewer = FrontendViewer::get()
			->setTitle('Запуск задачи')
			->setTopMenuActiveItem('tasks');
		
		if (!$instance->hasGridjobFile()){
			$viewer
				->setContentPhpFile(self::TPL_PATH.'xrsl_empty.php', array('id' => $instanceId))
				->render();
			return;
		}
		
		$userProfile = CurUser::get()->getFieldPrepared('profile');
		
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'myproxyLoginForm' => MyproxyServerController::snippet_myproxy_login(),
			'numSubmits' => $instance->getNumSubmits(),
			'taskFetchNotify' => !empty($userProfile['task_fetch_notify']),
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
			if ($instance->getField('uid') != USER_AUTH_ID)
				throw new Exception('Задача не принадлежит вам!');
			
			$fullname = $instance->getValidFileName($fname);
			if (empty($fullname))
				throw new Exception('Файл не найден #1');
			
			$vars = array(
				'instanceId' => $id,
				'fname' => $fname,
				'content' => file_get_contents($fullname),
				'file_size' => formatHumanReadableSize(filesize($fullname)),
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
		$instance = TaskSet::load($id);
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
		
		$fullname = $instance->getValidFileName($fname);
		if (empty($fullname))
			throw new Exception('Файл не найден #1');
		
		$fileType = TaskSet::getFileType($fullname);
		if (empty($fileType))
			throw new Exception('Неизвестный тип файла');
		
		$vars = array(
			'instanceId' => $id,
			'fname' => $fname,
			'formData' => TaskSet::getFileConstructor($fileType, $fullname)->getConstructorFormData(),
			'formFile' => TaskSet::getFormPath($fileType),
			'file_size' => formatHumanReadableSize(filesize($fullname)),
			'num_submits' => $instance->getNumSubmits(),
			'num_variants_in_file' => $instance->getFileVariantsNum($fullname, TRUE),
		);
		
		echo FrontendViewer::get()->getContentPhpFile(self::TPL_PATH.'file_constructor.php', $vars);
	}
	
	/** DISPLAY DELETE (ADMIN) */
	public function display_delete($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSet::Load($instanceId);
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');

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
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
		
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
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
		
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
		
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
		
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
		
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
		
		$fullname = $instance->getValidFileName($fname);
		if (empty($fullname))
			throw new Exception('Файл не найден #1');
			
		$fileType = TaskSet::getFileType($fullname);
		if (empty($fileType))
			throw new Exception('Неизвестный тип файла');
		
		TaskSet::getFileConstructor($fileType, $fullname)->saveConstructorFormData(Tools::unescape($_POST['items']), $instance);
		
		FrontendViewer::get()->setVariables(array('saved_success' => true));
		return TRUE;
	}
	
	public function action_submit($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskSet::load($instanceId);
		
		if ($instance->getField('uid') != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');
		
		// получение авторизационных данных myproxy
		try {
			$connector = MyproxyConnector::createByConnectForm($_POST);
		} catch (Exception $e) {
			Messenger::get()->addError(Lng::get('task.warnings'), $e->getMessage());
			return FALSE;
		}
		
		if($report = $instance->submit($connector, getVar($_POST['prefer-server']), !empty($_POST['email-notify']))){
		
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
	
	public function ajax_get_statuses($params = array()){
		
		$setId = getVar($_GET['set_id'], 0, 'int');
		$collection = TaskSubmitCollection::load()->getTasksBySet($setId, TRUE);
		foreach ($collection as $i => $v){
			$collection[$i]['status'] = $collection[$i]['status'] ? Lng::get($collection[$i]['title']) : Lng::get('task.state.');
		}
		echo json_encode($collection);
	}
	
	/** DISPLAY LIST */
	public function ajax_list($params = array()){ /* ужасный костыль, pt #2 */
		
		$collection = new TaskSetCollection(array('uid' => USER_AUTH_ID, 'search' => getVar($_GET['search'])));
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		AjaxViewer::get()
			->setContentPhpFile(self::TPL_PATH.'list.php', $variables)
			->render();
	}
	
	/** DISPLAY VIEW */
	public function ajax_view($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		$data = TaskSet::Load($instanceId)->GetAllFieldsPrepared();
		if ($data['uid'] != USER_AUTH_ID)
			throw new Exception('Задача не принадлежит вам!');

		$submits = TaskSubmitCollection::load(array('set_id' => $instanceId));
		
		$variables = array_merge($data, array(
			'instanceId' => $instanceId,
			'submits' => $submits->getPaginated(),
			'submitPagination' => $submits->getPagination(),
			'submitSorters' => $submits->getSortableLinks(),
		));
		
		// echo '<pre>'; print_r(TaskSubmitCollection::load()->getTasksBySet($instanceId)); die;
		AjaxViewer::get()
			->setContentPhpFile(self::TPL_PATH.'view.php', $variables)
			->render();
	}
	
	/** DISPLAY STATISTICS */
	public function ajax_statistics($params = array()){
		
		if ($curSet = getVar($params[0], 0, 'int')) {
			$this->_ajax_set_statistics($curSet);
			exit;
		}
		
		$collection = new TaskSetCollection();
		$variables = array(
			'collection' => $collection->getPaginated(array('withUsers' => TRUE)),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		AjaxViewer::get()
			->setContentPhpFile(self::TPL_PATH.'statistics.php', $variables)
			->render();
	}
	
	public function _ajax_set_statistics($instanceId){
		
		$submits = TaskSubmitCollection::load(array('set_id' => $instanceId));
		
		$data = TaskSet::Load($instanceId)->GetAllFieldsPrepared();
		$variables = array_merge($data, array(
			'instanceId' => $instanceId,
			'submits' => $submits->getPaginated(),
			'submitPagination' => $submits->getPagination(),
			'submitSorters' => $submits->getSortableLinks(),
		));
		
		AjaxViewer::get()
			->setContentPhpFile(self::TPL_PATH.'set_statistics.php', $variables)
			->render();
	}

}

?>
