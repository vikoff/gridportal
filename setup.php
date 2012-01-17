<?

if(!defined('FS_ROOT'))
	die('access denided (setup file)');
	
$GLOBALS['__vikOffTimerStart__'] = microtime(1);

if (!defined('IS_CLI'))
	define('IS_CLI', PHP_SAPI === 'cli');


########## ПОДКЛЮЧЕНИЕ ЯДРА ##########

require_once(FS_ROOT.'core/Func.core.php');
require_once(FS_ROOT.'core/App.core.php');


########## УСТАНОВКА ОБРАБОТЧИКА ОШИБОК ##########

set_error_handler(array('Error', 'error_handler'));	


########## ПРАВА ПОЛЬЗОВАТЕЛЕЙ ##########

define('PERMS_UNREG', 		0);
define('PERMS_REG', 		10);
define('PERMS_MODERATOR',	20);
define('PERMS_ADMIN', 		30);
define('PERMS_SUPERADMIN',	40);
define('PERMS_ROOT', 		50);


########## ПОДКЛЮЧЕНИЕ КОНФИГУРАЦИОННОГО ФАЙЛА ##########

require_once('config.php');


########## ИНИЦИАЛИЗАЦИЯ ОБЪЕКТА REQUEST ##########

Request::get();


########## ИНИЦИАЛИЗАЦИЯ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ ##########

try {
	CurUser::init();
} catch (Exception $e) {
	CurUser::logout();
	App::reload();
}

define('USER_AUTH_ID', CurUser::get()->getAuthData('id'));
define('USER_AUTH_PERMS', CurUser::get()->getAuthData('perms'));


########## ПРОЧЕЕ ##########

define('FORMCODE', App::getFormCodeInput());

?>