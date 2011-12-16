<?php

class UserController extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'User/';
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = FALSE;
	protected $_defaultBackendDisplay = 'list';
	
	// права на выполнение методов контроллера
	public $permissions = array(
		
		'admin_display_list'	=> PERMS_ADMIN,
		'admin_display_view'	=> PERMS_ADMIN,
		'admin_display_delete'	=> PERMS_ADMIN,
		'admin_display_create'	=> PERMS_ADMIN,

		'action_save_perms' 	=> PERMS_ADMIN,
		'action_delete' 		=> PERMS_ADMIN,
		'action_create' 		=> PERMS_ADMIN,
	);
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	// DISPLAY LIST (ADMIN)
	public function admin_display_list($params = array()){
		
		$collection = new UserCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendViewer::get()
			->prependTitle('Список пользователей')
			->setLinkTags($collection->getLinkTags())
			->setContentSmarty(self::TPL_PATH.'admin_list.tpl', $variables);
	}
	
	// DISPLAY VIEW (ADMIN)
	public function admin_display_view($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = User::load($instanceId);
			
			$userPerms = $instance->getField('level');
			$perms = array('allowEdit' => FALSE, 'list' => '', 'curTitle' => User::getPermName($userPerms));
			if(USER_AUTH_PERMS >= $userPerms){
				$perms['allowEdit'] = TRUE;
				foreach(User::getPermsList() as $perm)
					if($perm > 0 && $perm <= USER_AUTH_PERMS)
						$perms['list'] .= '<option value="'.$perm.'" '.($perm == $userPerms ? 'selected="selected" style="color: blue;"' : '').'>'.User::getPermName($perm).'</option>';
			}
			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
				'perms' => $perms,
			));
			
			BackendViewer::get()
				->prependTitle('Данные пользователя')
				->setContentSmarty(self::TPL_PATH.'view.tpl', $variables);
		}
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
		}
		
	}
	
	// DISPLAY DELETE (ADMIN)
	public function admin_display_delete($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = User::Load($instanceId);

			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
			));
			
			BackendViewer::get()
				->prependTitle('Удаление пользователя #'.$instanceId)
				->setContentSmarty(self::TPL_PATH.'delete.tpl', $variables);
		}
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
		}
		
	}
	
	// DISPLAY CREATE (ADMIN)
	public function admin_display_create($params = array()){
		
		$instance = User::Create();

		$variables = null;//array_merge($instance->GetAllFieldsPrepared(), array(
		//	'instanceId' => $instanceId,
		//));
		
		BackendViewer::get()
			->prependTitle('Создание нового пользователя')
			->setContentSmarty(self::TPL_PATH.'admin_create.tpl', $variables);
		
	}
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	// ACTION SAVE PERMS
	public function action_save_perms($params = array()){
		
		$instanceId = getVar($_POST['instance-id'], 0, 'int');
		
		try{
			$instance = User::Load($instanceId);
		
			if($instance->setPerms(getVar($_POST['level'], 0, 'int'))){
				Messenger::get()->addSuccess('Пользователь получил новые права');
				return TRUE;
			}else{
				Messenger::get()->addError('Не удалось установить новые права для пользователя:', $instance->getError());
				return FALSE;
			}
		}
		catch(Exception $e){
			BackendViewer::get()->error404();
		}

	}
	
	// ACTION DELETE (ADMIN)
	public function action_delete($params = array()){
		
		$instanceId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$instance = User::Load($instanceId);
	
		if($instance->Destroy()){
			Messenger::get()->addSuccess('Пользователь удален');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось удалить пользователя.');
			return FALSE;
		}

	}
	
	// ACTION CREATE (ADMIN)
	public function action_create($params = array()){
		
		$instance = User::Create();
	
		if($instance->Save($_POST)){
			Messenger::get()->addSuccess('Пользователь создан');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось создать пользователя.');
			Messenger::get()->addError($instance->getError());
			return FALSE;
		}

	}
}

?>