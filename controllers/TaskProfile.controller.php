<?php

class TaskProfileController extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'TaskProfile/';
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = 'list';
	protected $_defaultBackendDisplay = 'list';
	
	// права на выполнение методов контроллера
	public $permissions = array(
		'display_list' 			=> PERMS_UNREG,
		'display_view' 			=> PERMS_UNREG,
		
		'admin_display_list'	=> PERMS_ADMIN,
		'admin_display_new'		=> PERMS_ADMIN,
		'admin_display_edit'	=> PERMS_ADMIN,
		'admin_display_copy'	=> PERMS_ADMIN,
		'admin_display_delete'	=> PERMS_ADMIN,

		'action_save' 			=> PERMS_ADMIN,
		'action_delete' 		=> PERMS_ADMIN,
		
		'action_upload_file'	=> PERMS_ADMIN,
		
		'ajax_get_task_files'	=> PERMS_REG,
		'ajax_delete_task_file'	=> PERMS_REG,
	);
	
	protected $_title = null;
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY LIST */
	public function display_list($params = array()){
		
		$collection = new TaskProfileCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		FrontendViewer::get()
			->setTitle('Коллекция')
			->setLinkTags($collection->getLinkTags())
			->setContentPhpFile(self::TPL_PATH.'list.php', $variables)
			->render();
	}
	
	/** DISPLAY VIEW */
	public function display_view($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		
			$variables = TaskProfile::Load($instanceId)->GetAllFieldsPrepared();
			FrontendViewer::get()
				->setTitle('Детально')
				->setContentPhpFile(self::TPL_PATH.'view.php', $variables)
				->render();
	}
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	/** DISPLAY LIST (ADMIN) */
	public function admin_display_list($params = array()){
		
		$collection = new TaskProfileCollection();
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
	
	/** DISPLAY NEW (ADMIN) */
	public function admin_display_new($params = array()){
		
		$pageTitle = 'Создание новой страницы';
		
		$variables = array_merge($_POST, array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'projects_list' => ProjectCollection::load()->getAll(),
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
	}
	
	/** DISPLAY EDIT (ADMIN) */
	public function admin_display_edit($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskProfile::Load($instanceId);
		
		$pageTitle = '<span style="font-size: 14px;">Редактирование элемента</span> #'.$instance->getField('id');
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'pageTitle'  => $pageTitle,
			'projects_list' => ProjectCollection::load()->getAll(),
		));
		
		BackendViewer::get()
			->prependTitle('Редактирование записи')
			->setBreadcrumbs('add', array(null, 'Редактирование записи'))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
		
	}
	
	/** DISPLAY COPY (ADMIN) */
	public function admin_display_copy($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskProfile::Load($instanceId);
		
		$pageTitle = 'Копирование записи';
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'projects_list' => ProjectCollection::load()->getAll(),
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
		
	}
	
	/** DISPLAY DELETE (ADMIN) */
	public function admin_display_delete($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskProfile::Load($instanceId);

		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
		));
		
		BackendViewer::get()
			->prependTitle('Удаление записи')
			->setBreadcrumbs('add', array(null, 'Удаление записи'))
			->setContentPhpFile(self::TPL_PATH.'delete.php', $variables);
		
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION SAVE (ADMIN) */
	public function action_save($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new TaskProfile($instanceId);
		
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
		$instance = TaskProfile::Load($instanceId);
		
		// установить редирект на admin-list
		$this->setRedirectUrl('admin/content/task-profile/list');
	
		if($instance->Destroy()){
			Messenger::get()->addSuccess('Запись удалена');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось удалить запись:', $instance->getError());
			// выполнить редирект принудительно
			$this->forceRedirect();
			return FALSE;
		}

	}
	
	/** ACTION UPLOAD-FILE */
	public function action_upload_file($params = array()){
		
		// App::stopDisplay();
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskProfile::Load($instanceId);
		
		if(!User::hasPerm(PERMS_ADMIN) && $instance->getField('uid') != USER_AUTH_ID){
			echo 'Указанный профиль не пренадлежит вам';
			return FALSE;
		}
		
		if(empty($_FILES)){
			echo 'Файл не передан';
			return FALSE;
		}

		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = $instance->getFilesDir();
		
		if(!is_dir($targetPath))
			mkdir($targetPath, 0777, TRUE);
		
		$targetName = $_FILES['Filedata']['name'];
		$targetFullName =  $targetPath.$targetName;
		move_uploaded_file($tempFile, $targetFullName);
			
		return TRUE;
	}
	

	//////////////////
	////// AJAX //////
	//////////////////
	
	/** AJAX GET-TASK-FILES */
	public function ajax_get_task_files($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0, 'int');
			$instance = TaskProfile::Load($instanceId);
			
			if(!User::hasPerm(PERMS_ADMIN) && $instance->getField('uid') != USER_AUTH_ID){
				echo '{"error": "Указанный профиль не пренадлежит вам"}';
				return FALSE;
			}
			
			$elms = $instance->getAllFilesList();
			foreach($elms as &$elm)
				$elm = '"'.$elm.'"';
			
			echo '{"error": "", "data": ['.implode(',', $elms).']}';
			return TRUE;
		}
		catch(Exception $e){
			echo '{"error": "Профиль не найден"}';
			return FALSE;
		}
	}
	
	/** AJAX DELETE-TASK-FILE */
	public function ajax_delete_task_file($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		$instance = TaskProfile::Load($instanceId);
		
		if(!User::hasPerm(PERMS_ADMIN) && $instance->getField('uid') != USER_AUTH_ID){
			echo 'Указанный профиль не пренадлежит вам';
			return FALSE;
		}
		
		$taskDir = $instance->getFilesDir();
		
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