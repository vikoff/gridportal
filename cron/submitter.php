<?

session_start();
ini_set('display_errors', 1);

// обозначение корня ресурса
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// отправка Content-type заголовка
header('Content-Type: text/html; charset=utf-8');

// подключение файлов CMF
require_once(FS_ROOT.'setup.php');

function logMsg($msg){
	$logFile = dirname(__FILE__).'/log/submitter.log';
	$text = date('Y-m-d H:i:s').' '.$msg."\n";
	echo $text;
	// return;
	$f = fopen($logFile, 'a') or die('could not open log file');
	fwrite($f, $text);
	fclose($f);
}

if ($argc < 2) {
	logMsg("no trigger task id specified");
	exit;
}

$db = db::get();
$triggerTask = (int)$argv[1];
$taskIds = $db->getCol('SELECT dependent_task_id FROM task_submit_queue WHERE trigger_task_id='.$triggerTask);

if (empty($taskIds)) {
	logMsg("task has no dependent tasks");
	exit;
}

$preferedServer =  $db->getOne('SELECT prefered_server FROM task_submits WHERE id='.$triggerTask);
logMsg("prefered server: ".$preferedServer);

$myProxyAuthData = null;
foreach ($taskIds as $id) {
	
	$submitInstance = TaskSubmit::load($id);
	
	// загрузка данных myproxy
	if (empty($myProxyAuthData)) {
		$myProxyAuthData = User::load($submitInstance->getUid())->getMyproxyLoginData();
		if (empty($myProxyAuthData)) {
			logMsg('myproxy auth data for user #'.$submitInstance->getUid().' NOT FOUND');
			exit();
		}
		$_myproxyServer = MyproxyServer::load($myProxyAuthData['serverId'])->getAllFields();
		$myProxyAuthData['url'] = $_myproxyServer['url'];
		$myProxyAuthData['port'] = $_myproxyServer['port'];
	}
	
	$submitInstance->submit($myProxyAuthData, $preferedServer);
	logMsg('task #'.$submitInstance->id.' submitted');
	$db->delete('task_submit_queue', 'dependent_task_id='.$submitInstance->id);
}

?>