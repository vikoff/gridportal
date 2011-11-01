<?php

class ProjectController extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'Project/';
	
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
	);
	
	protected $_title = null;
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY LIST */
	public function display_list($params = array()){
		
		$collection = new ProjectCollection();
		$variables = array(
			'projectsList' => ProjectCollection::load()->getAll(),
			'userAllowedProjects' => CurUser::get()->getAllowedProjects(),
		);
		
		// echo '<pre>'; print_r($variables); die;
		
		FrontendViewer::get()
			->setTitle('Проекты')
			->setLinkTags($collection->getLinkTags())
			->setTopMenuActiveItem('projects')
			->setContentPhpFile(self::TPL_PATH.'list.php', $variables)
			->render();
	}
	
	/** DISPLAY VIEW */
	public function display_view($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		
			$variables = Project::Load($instanceId)->GetAllFieldsPrepared();
			FrontendViewer::get()
				->setTitle('Детально')
				->setContentSmarty(self::TPL_PATH.'view.tpl', $variables)
				->render();
	}
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	/** DISPLAY LIST (ADMIN) */
	public function admin_display_list($params = array()){
		
		$collection = new ProjectCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendViewer::get()
			->prependTitle('Список элементов')
			->setLinkTags($collection->getLinkTags())
			->setContentSmarty(self::TPL_PATH.'admin_list.tpl', $variables);
	}
	
	/** DISPLAY NEW (ADMIN) */
	public function admin_display_new($params = array()){
		
		$pageTitle = 'Создание новой страницы';
		
		$variables = array_merge($_POST, array(
			'instanceId'  => 0,
			'pageTitle'   => $pageTitle,
			'validation'  => Project::Create()->getValidator()->getJsRules(),
			'allowedVoms' => array(),
			'vomsList'    => VomsCollection::load()->getAll(),
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
	}
	
	/** DISPLAY EDIT (ADMIN) */
	public function admin_display_edit($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Project::Load($instanceId);
		
		$pageTitle = '<span style="font-size: 14px;">Редактирование элемента</span> #'.$instance->getField('id');
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId'  => $instanceId,
			'pageTitle'   => $pageTitle,
			'validation'  => $instance->getValidator()->getJsRules(),
			'allowedVoms' => $instance->getVoms(),
			'vomsList'    => VomsCollection::load()->getAll(),
		));
		
		// echo '<pre>'; print_r(); die;
		
		BackendViewer::get()
			->prependTitle('Редактирование записи')
			->setBreadcrumbs('add', array(null, 'Редактирование записи'))
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
		
	}
	
	/** DISPLAY COPY (ADMIN) */
	public function admin_display_copy($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Project::Load($instanceId);
		
		$pageTitle = 'Копирование записи';
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
		
	}
	
	/** DISPLAY DELETE (ADMIN) */
	public function admin_display_delete($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Project::Load($instanceId);

		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
		));
		
		BackendViewer::get()
			->prependTitle('Удаление записи')
			->setBreadcrumbs('add', array(null, 'Удаление записи'))
			->setContentSmarty(self::TPL_PATH.'delete.tpl', $variables);
		
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION SAVE (ADMIN) */
	public function action_save($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new Project($instanceId);
		
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
		$instance = Project::Load($instanceId);
		
		// установить редирект на admin-list
		$this->setRedirectUrl('admin/content/project/list');
	
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
	
}

?>