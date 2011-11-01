<?


require_once(FS_ROOT.'core/Func.core.php');

// создание подключения к mysql
db::create(array(
	'adapter' => 'mysql',
	'host' => 'localhost',
	'user' => 'gridjobs',
	'pass' => 'freedom2011gridjobs',
	'database' => 'gridjobs',
	'encoding' => 'utf8',
	'fileLog' => FALSE,
));

?>