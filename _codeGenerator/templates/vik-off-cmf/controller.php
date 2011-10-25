%%%	USE PLACEHOLDERS:
%%%		__CONTROLLERNAME__
%%%		__MODELNAME__
%%%		__MODEL_NAME_LOW__
<?php

class __CONTROLLERNAME__ extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = '__MODELNAME__/';
	
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
		'admin_display_delete'	=> PERMS_ADMIN,

		'action_save' 			=> PERMS_ADMIN,
		'action_delete' 		=> PERMS_ADMIN,
	);
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	// DISPLAY LIST
	public function display_list($params = array()){
		
		$collection = new __MODELNAME__Collection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		FrontendViewer::get()
			->setTitle('Коллекция')
			->setLinkTags($collection->getLinkTags())
			->setContentSmarty(self::TPL_PATH.'list.tpl', $variables)
			->render();
	}
	
	// DISPLAY VIEW
	public function display_view($params = array()){
		
		$instanceId = getVar($params[0], 0, 'int');
		
		try{
			$variables = __MODELNAME__::Load($instanceId)->GetAllFieldsPrepared();
			FrontendViewer::get()
				->setTitle('Детально')
				->setContentSmarty(self::TPL_PATH.'view.tpl', $variables)
				->render();
		}
		catch(Exception $e){
			FrontendViewer::get()->error404('Запрашиваемая страница не найдена (#'.__LINE__.')');
		}
	}
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	// DISPLAY LIST (ADMIN)
	public function admin_display_list($params = array()){
		
		$collection = new __MODELNAME__Collection();
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
	
	// DISPLAY NEW (ADMIN)
	public function admin_display_new($params = array()){
		
		$variables = array_merge($_POST, array(
			'instanceId' => 0,
			'validation' => __MODELNAME__::Create()->getValidator()->getJsRules(),
			'redirect' => getVar($_POST['redirect']),
		));
		
		BackendViewer::get()
			->prependTitle('создание новой записи')
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
	}
	
	// DISPLAY EDIT (ADMIN)
	public function admin_display_edit($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = __MODELNAME__::Load($instanceId);
		
			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
				'redirect' => getVar($_POST['redirect']),
				'validation' => $instance->getValidator()->getJsRules(),
			));
			
			BackendViewer::get()
				->prependTitle('редактирование записи')
				->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
		}
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
		}
		
	}
	
	// DISPLAY DELETE (ADMIN)
	public function admin_display_delete($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = __MODELNAME__::Load($instanceId);

			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
			));
			
			BackendViewer::get()
				->prependTitle('Удаление записи')
				->setContentSmarty(self::TPL_PATH.'delete.tpl', $variables);
		}
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
		}
		
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	// ACTION SAVE (ADMIN)
	public function action_save($params = array()){
	
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new __MODELNAME__($instanceId);
		
		if($instance->Save($_POST)){
			Messenger::get()->addSuccess('Запись сохранена');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить запись:', $instance->getError());
			return FALSE;
		}
	}
	
	// ACTION DELETE (ADMIN)
	public function action_delete($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		
		try{
			$instance = __MODELNAME__::Load($instanceId);
			
			// установить редирект на admin-list
			$this->setRedirectUrl(App::href('admin/content/__MODEL_NAME_LOW__/list'));
		
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
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
		}

	}
}

?>