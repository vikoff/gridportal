<?php

class CoreController extends Controller{
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = null;
	protected $_defaultBackendDisplay = null;
	
	// права на выполнение методов контроллера
	public $permissions = array(

		'action_paginator_set_items_per_page'	=> PERMS_REG,
		'action_error_delete_item'				=> PERMS_ADMIN,
	);
	

	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	// ACTION PAGINATOR_SET_ITEMS_PER_PAGE
	public function action_paginator_set_items_per_page($params = array()){
		
		$num = $_POST['num'];
		if(isset(Paginator::$itemsPerPageVariants[$num]))
			$_SESSION['paginator-items-per-page'] = $num;
		else
			trigger_error('invalid num: '.$num);
	}
	
	// ACTION ACTION_ERROR_DELETE_ITEM
	public function action_error_delete_item($params = array()){
		
		$id = getVar($_POST['id'], 0, 'int');
		try{
			Error::load($id)->destroy();
			Messenger::get()->addSuccess('Запись удалена');
			return TRUE;
		}catch(Exception $e){
			Messenger::get()->addError('Не удалось удалить запись', $e->getMessage());
			return FALSE;
		}
	}
	
}

?>