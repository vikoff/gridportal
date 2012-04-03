<?

error_reporting(E_ALL);
ini_set('display_errors', 1);

// обозначение корня ресурса
define('CUR_PATH', dirname(__FILE__).'/');
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('IS_CLI', TRUE);

// отправка Content-type заголовка
header('Content-Type: text/plain; charset=utf-8');

require(CUR_PATH.'setup.php');

require(FS_ROOT.'setup.php');

Mail::create()->send(2, 'fetch_success', array(
	'jobid' => 'gsiftp://ololo/param-val',
	'task_status' => 'task.state.submitted',
	'task_href' => 'http://google.com.ua',
));

echo "mail sent\n";

?>
