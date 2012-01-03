<?

session_start();
ini_set('display_errors', 1);

// обозначение текущий папки
define('CUR_PATH', dirname(__FILE__).'/');

// обозначение корня ресурса
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// отправка Content-type заголовка
header('Content-Type: text/html; charset=utf-8');

// подключение локального setup файла
require(CUR_PATH.'setup.php');

// подключение файлов CMF
require_once(FS_ROOT.'setup.php');

if ($argc < 2) {
	logMsg("no trigger task id specified", 'submitter');
	exit;
}

$db = db::get();
$triggerTask = (int)$argv[1];
$taskIds = $db->getCol('SELECT dependent_task_id FROM task_submit_queue WHERE error_code=0 AND trigger_task_id='.$triggerTask);

if (empty($taskIds)) {
	logMsg("task has no dependent tasks", 'submitter');
	exit;
}

$preferedServer =  $db->getOne('SELECT prefered_server FROM task_submits WHERE id='.$triggerTask);
$taskSetData = $db->getRow('SELECT s.* FROM task_sets s JOIN task_submits sb ON sb.set_id=s.id WHERE sb.id='.$triggerTask);
logMsg("prefered server: ".$preferedServer, 'submitter');

$connector = null;
foreach ($taskIds as $id) {
	
	$submitInstance = TaskSubmit::load($id);
	
	// загрузка данных myproxy
	if (empty($connector)) {
		$connector = getMyproxyConnector( $submitInstance->getUid(), $taskIds );
	}
	
	$submitInstance->submit($connector, $preferedServer, $taskSetData);
	logMsg('task #'.$submitInstance->id.' submitted', 'submitter');
	$db->delete('task_submit_queue', 'dependent_task_id='.$submitInstance->id);
}

?>
