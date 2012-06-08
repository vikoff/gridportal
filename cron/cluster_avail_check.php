<?php
ini_set('display_errors', 1);

// обозначение текущий папки
define('CUR_PATH', dirname(__FILE__).'/');

// обозначение корня ресурса
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('IS_CLI', TRUE);

// отправка Content-type заголовка
header('Content-Type: text/html; charset=utf-8');

// подключение локального setup файла
require(CUR_PATH.'setup.php');

// подключение файлов CMF
require_once(FS_ROOT.'setup.php');

// чтение данных
$fileArr = file('ftp://web.bitp.kiev.ua/pub/nagios/status/service.stat');

// print_r($fileArr);

$curHost = array('host' => '');
$lastService = null;
$services = array(
	'authentification test' => 'authentification_status',
	'certificate test'		=> 'certificate_status',
	'gcc'					=> 'gcc_status',
	'gridftp test' 			=> 'grid_ftp_status',
	'host alive' 			=> 'host_alive_status',
	'infosys' 				=> 'infosys_status',
	'jobsubmit'				=> 'job_submit_status',
	'softver' 				=> 'softver_status',
);
$numHosts = 0;

function saveHostStatus($data){
	
	if (!$data['host'])
		return;
		
	$GLOBALS['numHosts']++;
	$db = db::get();
	
	// common status
	$data['status'] = max($data['grid_ftp_status'], $data['host_alive_status'],
	                      $data['infosys_status'], $data['job_submit_status']);
	
	// if host exists in db
	if ($db->getOne('SELECT COUNT(1) FROM clusters_availability WHERE host='.$db->qe($data['host']))) {
		db::get()->update('clusters_availability', $data, 'host='.$db->qe($data['host']));
	}
	// if host does not exists
	else {
		db::get()->insert('clusters_availability', $data);
	}
}

foreach ($fileArr as $row) {
	
	$row = strtolower(trim($row));
	if (empty($row))
		continue;
	
	list($key, $value) = explode('=', $row) + array('', '');
	
	$key = trim($key);
	$value = trim($value);
	
	switch ($key) {
		case 'host':
			if ($curHost['host'] != $value) {
				saveHostStatus($curHost);
				$curHost = array('host' => $value);
			}
			break;
		case 'service':
			// echo 'catch service'." $value\n";
			$lastService = $services[$value];
			break;
		case 'status':
			if (!$lastService)
				continue 2;
			$curHost[ $lastService ] = $value;
			break;
	}
}

saveHostStatus($curHost);

// сохранение нерабочих хостов в файл clusters_unavail.txt
$unavailClusters = db::get()->getCol('SELECT host FROM clusters_availability WHERE status > 0');
file_put_contents(FS_ROOT.'clusters_unavail.txt', implode("\n", $unavailClusters)."\n");

echo "$numHosts clusters parsed.\n";

// Файл необходимо добавить в cron например так : 
// */5 * * * *       root  /usr/bin/php -q /var/www/portal/cron/cluster_avail_check.php