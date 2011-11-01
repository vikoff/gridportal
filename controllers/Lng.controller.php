<?php

class LngController extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'Lng/';
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = null;
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
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	/** DISPLAY LIST (ADMIN) */
	public function admin_display_list($params = array()){
		
		// echo '<pre>'; print_r(Lng::getAll()); die;
		$variables = array(
			'collection' => Lng::getAll(),
			'lngs' => Lng::$allowedLngs,
		);
		
		BackendViewer::get()
			->prependTitle('Языковые фрагменты')
			->setContentPhpFile(self::TPL_PATH.'admin_list.php', $variables);
	}
	
	/** DISPLAY NEW (ADMIN) */
	public function admin_display_new($params = array()){
		
		$pageTitle = 'Создание языкового фрагмента';
		
		$variables = array_merge($_POST, array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'lngs' => Lng::$allowedLngs,
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
	}
	
	/** DISPLAY EDIT (ADMIN) */
	public function admin_display_edit($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$data = Lng::getSnippetAllData($instanceId);
		if(empty($data))
			throw new Exception404('Языковой фрагмент не найден');
		
		$pageTitle = '<span style="font-size: 14px;">Редактирование языкового фрагмента</span> '.$data['name'];
	
		$variables = array_merge($data, array(
			'instanceId' => $instanceId,
			'pageTitle'  => $pageTitle,
			'lngs' => Lng::$allowedLngs,
		));
		
		BackendViewer::get()
			->prependTitle('Редактирование записи')
			->setBreadcrumbs('add', array(null, 'Редактирование записи'))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
		
	}
	
	/** DISPLAY COPY (ADMIN) */
	public function admin_display_copy($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$data = Lng::getSnippetAllData($instanceId);
		if(empty($data))
			throw new Exception404('Языковой фрагмент не найден');
		
		$pageTitle = 'Копирование языкового фрагмента';
	
		$variables = array_merge($data, array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'lngs' => Lng::$allowedLngs,
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables);
		
	}
	
	/** DISPLAY DELETE (ADMIN) */
	public function admin_display_delete($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$data = Lng::getSnippetAllData($instanceId);
		if(empty($data))
			throw new Exception404('Языковой фрагмент не найден');

		$variables = array_merge($data, array(
			'instanceId' => $instanceId,
		));
		
		BackendViewer::get()
			->prependTitle('Удаление языкового фрагмента')
			->setBreadcrumbs('add', array(null, 'Удаление языкового фрагмента'))
			->setContentPhpFile(self::TPL_PATH.'delete.php', $variables);
		
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION SAVE (ADMIN) */
	public function action_save($params = array()){
		
		$result = Lng::get()->save($_POST);
		
		if($result == 'ok'){
			Messenger::get()->addSuccess('Запись сохранена');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить запись:', $result);
			return FALSE;
		}
	}
	
	/** ACTION DELETE (ADMIN) */
	public function action_delete($params = array()){
		
		$instanceId = getVar($_POST['id'], 0 ,'int');
		if(empty($instanceId))
			throw new Exception404('Языковой фрагмент не найден');
		
		// установить редирект на admin-list
		$this->setRedirectUrl('admin/root/lng/list');
		$result = Lng::get()->delete($instanceId);
		
		if($result == 'ok'){
			Messenger::get()->addSuccess('Запись удалена');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось удалить запись:', $result);
			// выполнить редирект принудительно
			$this->forceRedirect();
			return FALSE;
		}

	}
	
}

?>