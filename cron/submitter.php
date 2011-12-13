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

function getMyproxyConnector($uid, $taskIds){
	
	$myProxyAuthData = User::load($uid)->getMyproxyLoginData();
	if (empty($myProxyAuthData)) {
		db::get()->update('task_submit_queue', array('error_code' => 1), 'dependent_task_id IN ('.implode(',', $taskIds).')');
		logMsg('ERROR: myproxy auth data for user #'.$uid.' NOT FOUND');
		exit();
	}
	$_myproxyServer = MyproxyServer::load($myProxyAuthData['serverId'])->getAllFields();
	$myProxyAuthData['url'] = $_myproxyServer['url'];
	$myProxyAuthData['port'] = $_myproxyServer['port'];
	
	return new MyproxyConnector($myProxyAuthData);
}

if ($argc < 2) {
	logMsg("no trigger task id specified");
	exit;
}

$db = db::get();
$triggerTask = (int)$argv[1];
$taskIds = $db->getCol('SELECT dependent_task_id FROM task_submit_queue WHERE error_code=0 AND trigger_task_id='.$triggerTask);

if (empty($taskIds)) {
	logMsg("task has no dependent tasks");
	exit;
}

$preferedServer =  $db->getOne('SELECT prefered_server FROM task_submits WHERE id='.$triggerTask);
$taskSetData = $db->getRow('SELECT s.* FROM task_sets s JOIN task_submits sb ON sb.set_id=s.id WHERE sb.id='.$triggerTask);
logMsg("prefered server: ".$preferedServer);

$connector = null;
foreach ($taskIds as $id) {
	
	$submitInstance = TaskSubmit::load($id);
	
	// загрузка данных myproxy
	if (empty($connector)) {
		$connector = getMyproxyConnector( $submitInstance->getUid(), $taskIds );
	}
	
	$submitInstance->submit($connector, $preferedServer, $taskSetData);
	logMsg('task #'.$submitInstance->id.' submitted');
	$db->delete('task_submit_queue', 'dependent_task_id='.$submitInstance->id);
}

?>