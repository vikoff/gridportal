<?

class AjaxViewer extends CommonViewer{
	
	protected $_skin = 'blank';
	
	private static $_instance = null;
	
	
	/** ТОЧКА ВХОДА В КЛАСС (ПОЛУЧИТЬ ЭКЗЕМПЛЯР CommonViewer) */
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new AjaxViewer();
		
		return self::$_instance;
	}
	
	/** ИНИЦИАЛИЗАЦИЯ */
	protected function init(){

		//$this->_topMenu = new Menu('frontend-top');
	}
	
	public function error403($message = ''){
		
		// для неавторизованных пользователей вывести форму авторизации
		if(USER_AUTH_PERMS == PERMS_UNREG){
			
			$errorMessage = Messenger::get()->ns('login')->getAll();
			include($this->_tplPath.'login.php');
		}
		// для авторизованных пользователей показать страницу 403
		else{
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden'); // 'HTTP/1.1 403 Forbidden'
			
			$variables = array(
				'message' => User::hasPerm(Error::getConfig('minPermsForDisplay')) ? $message : '',
			);
			$this
				->setTitle('Доступ запрещен')
				->setContentPhpFile('403.php', $variables)
				->render();
		}
		
		exit();
	}
	
}

?>