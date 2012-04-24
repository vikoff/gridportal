<?php

class TaskSubmitController extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'TaskSubmit/';
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = 'list';
	protected $_defaultBackendDisplay = 'list';
	
	// права на выполнение методов контроллера
	public $permissions = array(
		'display_list' 			=> PERMS_UNREG,
		'display_view' 			=> PERMS_UNREG,
		'display_get_results'	=> PERMS_REG,
		'display_stop'			=> PERMS_REG,
		'display_delete'		=> PERMS_REG,
		'display_analyze'		=> PERMS_REG,
		'display_visualize'		=> PERMS_REG,
		'display_download_dir'	=> PERMS_REG,
		'display_download_file'	=> PERMS_REG,
		
		'admin_display_list'	=> PERMS_ADMIN,
		'admin_display_new'		=> PERMS_ADMIN,
		'admin_display_edit'	=> PERMS_ADMIN,
		'admin_display_copy'	=> PERMS_ADMIN,
		'admin_display_delete'	=> PERMS_ADMIN,

		'action_save' 			=> PERMS_ADMIN,
		'action_stop' 			=> PERMS_REG,
		'action_delete' 		=> PERMS_REG,
		'action_get_results'	=> PERMS_REG,
	);
	
	protected $_title = null;
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY LIST */
	public function display_list($params = array()){
		
		$collection = new TaskSubmitCollection();
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
		
			$variables = TaskSubmit::Load($instanceId)->GetAllFieldsPrepared();
			FrontendViewer::get()
				->setTitle('Детально')
				->setContentPhpFile(self::TPL_PATH.'view.php', $variables)
				->render();
	}
	
	/** DISPLAY GET-RESULTS */
	public function display_get_results($params = array()){
	
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSubmit::load($instanceId);
		$user = CurUser::get();
		
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'myproxyLoginForm' => MyproxyServerController::snippet_myproxy_login(),
		));
		
		FrontendViewer::get()
			->prependTitle('Сохранение результата задачи')
			->setContentPhpFile(self::TPL_PATH.'get_result.php', $variables)
			->render();
	}
	
	/** DISPLAY STOP */
	public function display_stop($params = array()){
	
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSubmit::load($instanceId);
		$user = CurUser::get();
		
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'myproxyLoginForm' => MyproxyServerController::snippet_myproxy_login(),
		));
		
		FrontendViewer::get()
			->prependTitle('Остановка задачи')
			->setContentPhpFile(self::TPL_PATH.'stop.php', $variables)
			->render();
	}
	
	/** DISPLAY DELETE */
	public function display_delete($params = array()){
		
		$taskIds = getVar($_GET['task'], array(), 'array');
		$collection = !empty($taskIds)
			? TaskSubmitCollection::load(array('uid' => USER_AUTH_ID, 'ids' => YArray::intvalsReturn($taskIds)))->getAll()
			: array();
		
		$variables = array(
			'collection' => $collection,
			'setId' => !empty($collection) ? $collection[ YArray::getFirstKey($collection) ]['set_id'] : '',
			'myproxyLoginForm' => MyproxyServerController::snippet_myproxy_login(),
		);
		
		FrontendViewer::get()
			->prependTitle('Удаление задачи')
			->setContentPhpFile(self::TPL_PATH.'delete.php', $variables)
			->render();
		
	}
	
	/** DISPLAY ANALYZE */
	public function display_analyze($params = array()){
		
		$path = getVar($_GET['path']);
		$curSubmitId = getVar($_GET['submit'], 0, 'int');
		$collection = TaskSubmitCollection::load(array('uid' => USER_AUTH_ID));
		
		$variables = array(
			'fetchedTasks' => $collection->getFetchedSubmits(),
			'curSubmitId' => $curSubmitId,
		);
		
		if ($curSubmitId) {
			$submitInstnce = TaskSubmit::load($curSubmitId);
			if ($submitInstnce->getField('is_fetched'))
				$variables['fileTree'] = $submitInstnce->getResultFiles($path);
			else 
				Messenger::get()->addError('Файлы задачи еще не получены');
		}
		
		FrontendViewer::get()
			->setTitle(Lng::get('task.analyze'))
			->setTopMenuActiveItem('analyze')
	        ->setContentPhpFile(self::TPL_PATH.'analyze.php', $variables)
			->render();
		
		exit;
		
		if(isset($_GET['act'])){
			switch($_GET['act']){
				case 'download-dir':
					$collection->downloadDir($path);
					exit;
			}
		}
		
		$fileTree = $collection->getFileTree($path);
		
	}
	
	public function display_download_dir($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		TaskSubmit::load($instanceId)->downloadDir(getVar($_GET['path']));
	}
	
	public function display_download_file($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		TaskSubmit::load($instanceId)->downloadFile(getVar($_GET['path']));
	}
	
	public function display_visualize($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSubmit::load($instanceId);
		$viewer = FrontendViewer::get();
		
		$html = $instance->getVisualization(getVar($_GET['path']));
		
		$variables = array(
			'visualization' => $html,
		);
		
		echo $viewer->getContentPhpFile(self::TPL_PATH.'visualization.php', $variables);
	}
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	/** DISPLAY LIST (ADMIN) */
	public function admin_display_list($params = array()){
		
		$collection = new TaskSubmitCollection();
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
			'validation' => TaskSubmit::Create()->getValidator()->getJsRules(),
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
	}
	
	/** DISPLAY EDIT (ADMIN) */
	public function admin_display_edit($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSubmit::Load($instanceId);
		
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
		$instance = TaskSubmit::Load($instanceId);
		
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
	
	/** DISPLAY DELETE (ADMIN) */
	public function admin_display_delete($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = TaskSubmit::Load($instanceId);

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
		$instance = new TaskSubmit($instanceId);
		
		if($instance->save($_POST)){
			Messenger::get()->addSuccess('Запись сохранена');
			$this->_redirectUrl = !empty($this->_redirectUrl) ? preg_replace('/\(%([\w\-]+)%\)/e', '$instance->getField("$1")', $this->_redirectUrl) : null;
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить запись:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION STOP */
	public function action_get_results($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskSubmit::load($instanceId);
		
		// получение авторизационных данных myproxy
		try {
			$connector = MyproxyConnector::createByConnectForm($_POST);
		} catch (Exception $e) {
			Messenger::get()->addError(Lng::get('task.warnings'), $e->getMessage());
			return FALSE;
		}
	
		if($instance->getResults($connector)){
			Messenger::get()->addSuccess(Lng::get('xrls_edit.success'));
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось получить задачу:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION STOP */
	public function action_stop($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TaskSubmit::load($instanceId);
		
		// получение авторизационных данных myproxy
		try {
			$connector = MyproxyConnector::createByConnectForm($_POST);
		} catch (Exception $e) {
			Messenger::get()->addError(Lng::get('task.warnings'), $e->getMessage());
			return FALSE;
		}

		if($instance->stop($connector)){
			Messenger::get()->addSuccess('Задача остановлена и удалена.');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось остановить задачу:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION DELETE */
	public function action_delete($params = array()){
		
		$taskIds = getVar($_POST['task'], array(), 'array');
		if (!empty($taskIds)) {
			TaskSubmitCollection::load(array('uid' => USER_AUTH_ID, 'ids' => YArray::intvalsReturn($taskIds)))->delete();
			Messenger::get()->addSuccess('Все задачи удалены');
			return TRUE;
		} else {
			Messenger::get()->addError('Задачи не найдены');
		}
	}
	
}

?>