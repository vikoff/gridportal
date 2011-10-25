<?

session_start();
ini_set('display_errors', 1);

// обозначение корня ресурса
$_url = dirname($_SERVER['SCRIPT_NAME']);
define('PROTOCOL', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http');
define('WWW_ROOT', PROTOCOL.'://'.$_SERVER['SERVER_NAME'].(strlen($_url) > 1 ? $_url : '').'/');
define('WWW_URI', PROTOCOL.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
define('FS_ROOT', realpath('.').DIRECTORY_SEPARATOR);

// определение ajax-запроса
define('AJAX_MODE', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

// отправка Content-type заголовка
header('Content-Type: text/html; charset=utf-8');

// подключение файлов CMF
require_once(FS_ROOT.'setup.php');

// редирект на https
if(CFG_USE_HTTPS && PROTOCOL != 'https'){
    App::reload();
}
	
// контроллер отображения по умолчанию
define('DEFAULT_CONTROLLER', 'Page');

// выполнение приложения
if(AJAX_MODE)
	App::ajax();
else
	App::run();

// END
?>