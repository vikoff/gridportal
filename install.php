<?php
session_start();

define('WWW_ROOT', 'http://'.$_SERVER['SERVER_NAME'].(strlen(dirname($_SERVER['SCRIPT_NAME'])) > 1 ? dirname($_SERVER['SCRIPT_NAME']) : '').'/');
define('FS_ROOT', realpath('.').DIRECTORY_SEPARATOR);
define('YNPROJECT', 1);
define('AJAXMODE', 0);

header('Content-Type: text/html; charset=utf-8');
require_once('config.php');

$tables = db::get()->showTables();

echo"<pre>";
print_r($tables);
echo"</pre>";

if(!in_array("tasks", $tables)){

	db::get()->query("
		CREATE TABLE tasks (
			id 		INTEGER UNSIGNED PRIMARY KEY,
			uid		INTEGER UNSIGNED,
			parent	INTEGER UNSIGNED,
			title	TEXT,
			body	TEXT,
			urgency	VARCHAR(255),
			state	VARCHAR(255),
			deleted	CHAR(1) DEFAULT '0',
			date	INT UNSIGNED
		)
	");
	
	echo"<div>таблица <b>tasks</b> создана</div>";
}

if(!in_array("comments", $tables)){

	db::get()->query("
		CREATE TABLE comments (
			id 		INTEGER UNSIGNED PRIMARY KEY,
			uid		INTEGER UNSIGNED,
			trg		INTEGER UNSIGNED,
			body	TEXT,
			date	INT UNSIGNED
		)
	");
	
	echo"<div>таблица <b>comments</b> создана</div>";
}

?>