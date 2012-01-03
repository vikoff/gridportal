<?

function getMyproxyConnector($uid, $taskIds = null){
	
	$myProxyAuthData = User::load($uid)->getMyproxyLoginData();
	if (empty($myProxyAuthData)) {
		if (!empty($taskIds))
			db::get()->update('task_submit_queue', array('error_code' => 1), 'dependent_task_id IN ('.implode(',', $taskIds).')');
		logMsg('ERROR: myproxy auth data for user #'.$uid.' NOT FOUND', 'myproxy-error');
		exit();
	}
	$_myproxyServer = MyproxyServer::load($myProxyAuthData['serverId'])->getAllFields();
	$myProxyAuthData['url'] = $_myproxyServer['url'];
	$myProxyAuthData['port'] = $_myproxyServer['port'];
	
	return new MyproxyConnector($myProxyAuthData);
}

function logMsg($msg, $type = 'common'){
	
	$logFile = dirname(__FILE__).'/log/submitter.log';
	$text = date('Y-m-d H:i:s').' '.$msg."\n";
	echo $text;
//	return;
	$f = fopen($logFile, 'a') or die('could not open log file');
	fwrite($f, $text);
	fclose($f);
}

?>
