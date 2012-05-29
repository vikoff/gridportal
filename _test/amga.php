<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

// обозначение корня ресурса
define('CUR_PATH', dirname(__FILE__).'/');
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('IS_CLI', TRUE);

// отправка Content-type заголовка
header('Content-Type: text/plain; charset=utf-8');

require(FS_ROOT.'includes/mdclient2.php');

try{
	$client = new MDClient();
	$client->InitializeMDClient('127.0.0.1', '8822', 'root', 'freedom09com2009');
	// $client->requireSSL("x509_xxx", "x509_xxx");
	$client->connect();
} catch (Exception $e){
	echo 'Unable to connect: '. $e->getMessage(). "\n";
	exit;
}

echo $client->execute('ls');

echo "\n";
