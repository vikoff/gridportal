<?

if(empty($_POST['PHPSESSID']))
	die('Ошибка аутентификации (SID)');

session_id($_POST['PHPSESSID']);
session_start();

// обозначение корня ресурса
$_url = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])));
define('WWW_ROOT', 'http://'.$_SERVER['SERVER_NAME'].(strlen($_url) > 1 ? $_url : '').'/');
define('WWW_URI', 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
define('FS_ROOT', realpath('./../../').DIRECTORY_SEPARATOR);

// отправка Content-type заголовка
header('Content-Type: text/html; charset=utf-8');

// подключение файлов CMF
require_once(FS_ROOT.'setup.php');

// контроллер отображения по умолчанию
define('DEFAULT_CONTROLLER', 'Page');

// выполнение приложения
App::run();

?>