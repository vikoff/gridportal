<?
/**
 * Фронт-контроллер приложения. 
 * 
 * @using constants
 *		DEFAULT_CONTROLLER,
 *		CHECK_FORM_DUPLICATION,
 *		FS_ROOT,
 *		CFG_USE_SMARTY_CACHING,
 *		CFG_SMARTY_TRIMWHITESPACES,
 *		CFG_SITE_NAME,
 *		WWW_URI,
 *		WWW_PREFIX
 */
class App{
	
	public static $protocol = 'https';
	
	private static $stopDisplay = FALSE;
	
	public static $displayController = null;
	public static $displayMethodIdentifier = null;
	public static $displayMethodParams = array();
	
	public static $adminMode = FALSE;
	
	private static $_smartyInstance = null;

	
	#### ТОЧКИ ВХОДА В КЛАСС ####

	
	// ИНИЦИАЛИЗАЦИЯ ПРИЛОЖЕНИЯ
	static public function run(){
		
		// получение параметров из URL
		list($_controller, self::$displayMethodIdentifier, self::$displayMethodParams) = Request::get()->getArray();
		self::$adminMode = $_controller == 'admin';
		
		// определение контроллера отображения
		if(!self::$displayController = self::checkController($_controller)){
			self::error404('Controller "'.self::$displayController.'" does not exists');
			return;
		}
		
		// определение и выполнение действия
		self::checkAction();

		// определение отображения (если не приостановлено)
		if(!self::$stopDisplay){
		
			$displayControllerInstance = new self::$displayController();
			$displayControllerInstance->performDisplay(self::$displayMethodIdentifier, self::$displayMethodParams);
			unset($displayControllerInstance);
		}
	}
	
	// ИНИЦИАЛИЗАЦИЯ AJAX ЗАПРОСА
	static public function ajax(){
		
		list($_controller, $_method, $_params) = Request::get()->getArray();
		self::$adminMode = $_controller == 'admin';
		
		if(!$controllerClass = self::checkController($_controller)){
			self::error404('Controller "'.$controllerClass.'" does not exists');
		}
		
		$controllerInstance = new $controllerClass();
		
		$controllerInstance->performAjax($_method, $_params);
	}

	
	#### ПОДГОТОВКА К ВЫПОЛНЕНИЮ ОПЕРАЦИЙ ####
	
	// УСТАНОВИТЬ КОНТРОЛЛЕР
	public static function checkController($_controller, $strict = FALSE){
		
		// если контроллер не указан
		if(!strlen($_controller)){
			if($strict){
				return FALSE;
			}else{
				if(CFG_REDIRECT_DEFAULT_DISPLAY){
					App::redirectHref(Request::get()->getAppended(strtolower(DEFAULT_CONTROLLER)));
				}else{
					$_controller = DEFAULT_CONTROLLER;
				}
			}
		}

		return self::getControllerClassName($_controller);
	}
	
	// ПРОВЕРИТЬ НЕОБХОДИМОСТЬ ВЫПОЛЕННИЯ ДЕЙСТВИЯ
	public static function checkAction(){
		
		if(isset($_POST['cancel'])){
			if(!empty($_POST['redirect']))
				App::redirect($_POST['redirect']);
			return FALSE;
		}
		
		if(isset($_POST['action']) && App::checkFormDuplication()){
			
			$isArr = is_array($_POST['action']);
			$action = strtolower($isArr ? YArray::getFirstKey($_POST['action']) : $_POST['action']);
			$redirect = $isArr && is_array($_POST['action'][$action])
				? YArray::getFirstKey($_POST['action'][$action])
				: (isset($_POST['redirect']) ? $_POST['redirect'] : '');
			
			// параметр action должен иметь вид 'controller/method'
			if(strpos($action, '/') === FALSE){
				trigger_error('Неверный формат параметра action: '.$action.' (требуется разделитель)', E_USER_ERROR);
			}
			
			list($_controller, $_method) = YArray::trim(explode('/', $action));
			
			$controller = self::getControllerClassName($_controller);
			$method = self::getActionMethodName($_method);
			
			if(!$controller){
				CommonViewer::get()->error404('Контроллер '.$_controller.' не найден');
				exit();
			}
			
			$controllerInstance = new $controller();
			$controllerInstance->performAction($method, $redirect);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	// ПОЛУЧИТЬ ИМЯ КЛАССА КОНТРОЛЛЕРА ПО ИДЕНТИФИКАТОРУ
	// или FALSE в случае отсутствия класса
	public static function getControllerClassName($controller){
			
		if(!strlen($controller))
			return FALSE;
		
		// преобразует строку вида 'any-class-name' в 'AnyClassNameController'
		$controller = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($controller)))).'Controller';
		return class_exists($controller) ? $controller : FALSE;
	}
	
	// ПОЛУЧИТЬ ИМЯ МЕТОДА ОТОБРАЖЕНИЯ ПО ИДЕНТИФИКАТОРУ
	public static function getDisplayMethodName($method){
	
		// преобразует строку вида 'any-Method-name' в 'any_method_name'
		$method = 'display_'.(strlen($method) ? strtolower(str_replace('-', '_', $method)) : 'default');
		return $method;
	}
	
	// ПОЛУЧИТЬ ИМЯ МЕТОДА ДЕЙСТВИЯ ПО ИДЕНТИФИКАТОРУ
	public static function getActionMethodName($method){
	
		// преобразует строку вида 'any-Method-name' в 'any_method_name'
		$method = 'action_'.strtolower(str_replace('-', '_', $method));
		return $method;
	}
	
	// ПОЛУЧИТЬ ИМЯ AJAX МЕТОДА ПО ИДЕНТИФИКАТОРУ
	public static function getAjaxMethodName($method){
	
		// преобразует строку вида 'any-Method-name' в 'any_method_name'
		$method = 'ajax_'.strtolower(str_replace('-', '_', $method));
		return $method;
	}
	
	// ПРИОСТАНОВКА ОТОБРАЖЕНИЯ
	public static function stopDisplay($stop = TRUE){
	
		self::$stopDisplay = (bool)$stop;
	}
	
	
	#### ВЫПОЛНЕНИЕ РЕДИРЕКТОВ ####
	
	
	// REDIRECT
	public static function redirect($uri){
	
		// echo '<a href="'.$uri.'">'.$uri.'</a>'; die;
		header('location: '.$uri);
		exit();
	}
	
	// REDIRECT HREF
	public static function redirectHref($href){
		
		// trigger_error('redirect', E_USER_ERROR);
		// echo '<a href="'.App::href($href).'">'.App::href($href).'</a>'; die;
		header('location: '.App::href($href));
		exit();
	}
	
	// RELOAD
	public static function reload(){
	
		$url = PROTOCOL.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		header('location: '.$url);
		exit();
	}
	
	
	#### FORMCODE ####
	
	
	// ПОЛУЧИТЬ HTML INPUT СОДЕРЖАЩИЙ FORMCODE
	static public function getFormCodeInput(){
		return '<input type="hidden" name="formCode" value="'.self::_generateFormCode().'" />';
	}
	
	// ПРОВЕРКА ВАЛИДНОСТИ ФОРМЫ
	static public function checkFormDuplication(){
		
		if(isset($_POST['allowDuplication']))
			return TRUE;
			
		if(!isset($_POST['formCode'])){
			trigger_error('formCode не передан', E_USER_ERROR);
			return FALSE;
		}
		$formcode = (int)$_POST['formCode'];
		
		if(!CHECK_FORM_DUPLICATION)
			return TRUE;
			
		if(self::_isAllowedFormCode($formcode)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	// ПОМЕТИТЬ FORMCODE ИСПОЛЬЗОВАННЫМ
	static public function lockFormCode(&$code){
	
		if(CHECK_FORM_DUPLICATION && !empty($code))
			$_SESSION['userFormChecker']['used'][] = $code;
	}
	
	// СГЕНЕРИРОВАТЬ УНИКАЛЬНЫЙ FORMCODE
	static private function _generateFormCode(){
	
		// init session variable
		if(!isset($_SESSION['userFormChecker']))
			$_SESSION['userFormChecker'] = array('current' => 0, 'used' => array());
		// generate unique code
		$_SESSION['userFormChecker']['current']++;
		return $_SESSION['userFormChecker']['current'];
	}
	
	// ПРОВЕРИТЬ ПОЛУЧЕННЫЙ FORMCODE
	static private function _isAllowedFormCode($code){
	
		if(!$code)
			return FALSE;
		if(!isset($_SESSION['userFormChecker']['used']))
			return FALSE;
		return (bool)!in_array($code, $_SESSION['userFormChecker']['used']);
	}
	
	
	#### HREF ####
	
	/**
	 * HREF
	 * Генерация валидного абсолютного URL адреса
	 * @param string $href - строка вида 'contoller/method/addit?param1=val1&param2=val2
	 * return string абсолютный URL
	 */
	public static function href($href, $lng = null){
		
		$href = ($lng ? $lng : Lng::get()->getCurLng()).'/'.$href;
		return WWW_ROOT.(CFG_USE_SEF
			? $href											// http://site.com/controller/method?param=value
			: 'index.php?r='.str_replace('?', '&', $href));	// http://site.com/index.php?r=controller/method&param=value
	}
	
	/**
	 * GET HREF REPLACED
	 * Получить валидный url с замененным/добавленным параметром (одним или несколькими)
	 * @param string|array $nameOrPairs - имя параметра, или массив ($имя => $параметр)
	 * @param string|null $valueOrNull - значение параметра (если первый аргумент - строка) или null
	 * @return string валидный абсолютный URL с нужными параметрами
	 */
	public static function getHrefReplaced($nameOrPairs, $valueOrNull = null){
		
		// получить пары для замены
		$pairs = is_array($nameOrPairs)
			? $nameOrPairs
			: array($nameOrPairs => $valueOrNull);
		
		// определить, производится ли замена пути (r)
		$isPathReplaced = isset($pairs['r']);
		
		// получить копию $_GET с нужными заменами
		$copyOfGet = $_GET;
		foreach($pairs as $name => $value){
			if(is_null($value))	// если value == null, удалим параметр из QS
				unset($copyOfGet[$name]);
			else				// иначе добавим / заменим параметр в QS
				$copyOfGet[$name] = $value;
		}
		
		// сформировать валидный URL
		$r = $isPathReplaced
			? (isset($copyOfGet['r']) ? $copyOfGet['r'] : '')
			: Request::get()->getString();
		unset($copyOfGet['r']);
		$qs = array();
		foreach($copyOfGet as $k => $v)
			$qs[] = $k.'='.$v;
		
		return App::href($r.(count($qs) ? '?'.implode('&', $qs) : ''));
	}
	
	public static function getHrefLngReplaced($lng){
		
		$qs = array();
		foreach($_GET as $k => $v)
			if($k != 'r')
				$qs[] = $k.'='.$v;
		
		return App::href(Request::get()->getString().(count($qs) ? '?'.implode('&', $qs) : ''), $lng);
	}
	
	#### ПРОЧЕЕ ####
	
	// ERROR 403
	public static function error403($msg){
		
		if(AJAX_MODE){
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden'); // 'HTTP/1.1 403 Forbidden'
			echo $msg;
		}else{
			if(self::$adminMode)
				BackendViewer::get()->error403($msg);
			else
				FrontendViewer::get()->error403($msg);
		}
		exit();
	}
	
	// ERROR 404
	public static function error404($msg){
		
		if(AJAX_MODE){
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); // 'HTTP/1.1 404 Not Found'
			echo $msg;
		}else{
			if(self::$adminMode)
				BackendViewer::get()->error404($msg);
			else
				FrontendViewer::get()->error404($msg);
		}
		exit();
	}
	
	// ПОЛУЧИТЬ ЭКЗЕМПЛЯР SMARTY
	public static function smarty(){
	
		if(is_null(self::$_smartyInstance)){
		
			require_once(FS_ROOT.'includes/smarty/libs/Smarty.class.php');
			require_once(FS_ROOT.'includes/smarty/VIKOFF_SmartyPlugins.php');
			
			self::$_smartyInstance = new Smarty();
			
			$path = FS_ROOT.'includes/smarty/';
			
			self::$_smartyInstance->template_dir = FS_ROOT.'templates/';
			self::$_smartyInstance->compile_dir = $path.'templates_c/';
			self::$_smartyInstance->config_dir = $path.'configs/';
			self::$_smartyInstance->cache_dir = $path.'cache/';
			
			self::$_smartyInstance->caching = (bool)CFG_USE_SMARTY_CACHING;
			
			// использование подстановщиков в JS
			self::$_smartyInstance->register_prefilter(array('SmartyPlugins', 'escape_script'));
			
			// использование тега <a href=""></a> в шаблонах
			self::$_smartyInstance->register_function('a', array('SmartyPlugins', 'function_a'));
			
			// использование языковых фрагментов в шаблонах
			self::$_smartyInstance->register_function('lng', array('SmartyPlugins', 'function_lng'));
			
			// удаление всех лишних пробельных символов
			if(CFG_SMARTY_TRIMWHITESPACES)
				self::$_smartyInstance->register_prefilter(array('SmartyPlugins', 'trimwhitespace'));
			
			// назначение псевдоконстант
			self::$_smartyInstance->assign(array(
				'CFG_SITE_NAME'		=> CFG_SITE_NAME,	
				'WWW_ROOT' 			=> WWW_ROOT,
				'WWW_URI' 			=> WWW_URI,
			));
			
			// назначение других переменных
			self::$_smartyInstance->assign(array(
				'formcode' => self::getFormCodeInput(),
				'hasPermModerator' => (USER_AUTH_PERMS >= PERMS_MODERATOR),
				'hasPermAdmin' => (USER_AUTH_PERMS >= PERMS_ADMIN),
				'hasPermSuperadmin' => (USER_AUTH_PERMS >= PERMS_SUPERADMIN),
				'hasPermRoot' => (USER_AUTH_PERMS >= PERMS_ROOT),
			));
			
		}
		
		return self::$_smartyInstance;
	}

}
?>