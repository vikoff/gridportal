<?

if(!defined('WWW_ROOT'))
	die("access denided (config file)");

// название сайта
define('CFG_SITE_NAME', 'ng');

// установить уровень сообщений об ошибках (максимальный)
error_reporting(E_ALL | E_STRICT);

// установить текущий часовой пояс
date_default_timezone_set('Europe/Kiev');


########## EMAIL АДРЕСА ##########

// email разработчика
define('CFG_DEVELOPER_EMAIL', 'dev.yurijnovikov@gmail.com');

// email администратора сайта
define('CFG_ADMIN_EMAIL', 'yurijnovikov@gmail.com');


########## КОНФИГУРАЦИЯ ОБРАБОТЧИКА ОШИБОК ##########

Error::config(array(
	'display' => TRUE,				  // отображать ошибки или нет
	'minPermsForDisplay' => 0,		  // минимальные права для отображения ошибок
	'keepFileLog' => FALSE,			  // вести лог ошибок в файл
	'fileLogPath' => FS_ROOT.'logs/', // пусть хранения лог-файла
	'keepDbLog' => FALSE,			  // вести лог ошибок в базу данных
	'dbTableName' => 'error_log',	  // имя таблицы в БД
	'keepDbSessionDump' => TRUE,	  // сохранять дамп сессии пользователя (только при включенном DB-логе)
	'keepEmailLog' => FALSE,		  // отправлять сообщения об ошибках на email
));


########## СОЗДАНИЕ СОЕДИНЕНИЯ С БД ##########

// создание подключения к mysql
db::create(array(
	'adapter' => 'mysql',
	'host' => 'localhost',
	'user' => '',
	'pass' => '',
	'database' => '',
	'encoding' => 'utf8',
	'fileLog' => FALSE,
));

########## КОНФИГУРАЦИЯ КЛАССА СТАТИСТИКИ ##########

UserStatistics::config(array(
	'enable' => FALSE,
	'dbTableName' => 'user_statistics',
));


########## SMARTY ##########

// удаление лишних пробельных символов из html
define('CFG_SMARTY_TRIMWHITESPACES', 0);

// использовать ли кэширование шаблонов смарти
define('CFG_USE_SMARTY_CACHING', 0);

########## OTHER ##########

// использовать красивые url
define('CFG_USE_SEF', TRUE);

// отсеивать дублируемые формы
define('CHECK_FORM_DUPLICATION', 0);

// производить ли редирект на дефолтные display методы
define('CFG_REDIRECT_DEFAULT_DISPLAY', 0);

?>