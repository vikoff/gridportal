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
require(FS_ROOT.'includes/infosys/infosys.php');


// запуск скрипта получения выполненных задач
$script = CUR_PATH.'get_submits.php';
`php $script > /dev/null 2>$1 &`;

$db = db::get();
$jobs = $db->getAllIndexed('
	SELECT ts.*, sets.name, sets.gridjob_name, u.lng FROM task_submits ts
	JOIN users u ON u.id=ts.uid
	JOIN task_sets sets ON sets.id=ts.set_id
	WHERE 
		jobid IS NOT NULL AND LENGTH(jobid) > 0 AND
		is_submitted > 0 AND
		is_completed = 0
	ORDER BY ts.id
', 'jobid');

echo "TASKS ".print_r($jobs, 1)."\n";
// die; // DEBUG

if(empty($jobs))
	die('no jobs');
	
$allStatuses = $db->getAllIndexed('SELECT * FROM task_states', 'name');
	
$query = new BDIIQuery_ARCJobs(array( 
	"server" => "bdii.grid.org.ua",
	"port" => 2170,
	"basedn" => "Mds-Vo-name=local,o=grid"
));

$attrs = array ('nordugrid-job-status');
$statuses = $query->query_ARCJobs(array_keys($jobs), $attrs);

echo "RESPONSE ".print_r($statuses, 1)."\n";

if(empty($statuses))
	die('no statuses received');

foreach($statuses as $jobid => $data){
	
	// получение статуса
	$status = $data['nordugrid-job-status'];
	if(!isset($allStatuses[$status])) {
		$title = 'task.state.'.strtolower($status);
		$id = $db->insert('task_states', array('name' => $status, 'title' => $title));
		$allStatuses[$status] = array('id' => $id, 'name' => $status, 'title' => $title);
	}
	
	// изначально извлекаются только те задачи, у которых is_completed=0
	$isCompleted = 0;
	switch($status){
		case 'FINISHED': $isCompleted = 1; break;
		case 'FAILED':   $isCompleted = 2; break;
		case 'DELETED':  $isCompleted = 3; break;
	}
	
	$fields = array(
		'is_submitted' => 2,
		'is_completed' => $isCompleted,
		'status'       => $allStatuses[$status]['id'],
	);
	
	if ($isCompleted) {
		$fields['finish_date'] = time();
		if ($jobs[$jobid]['email_notify']) {
			
			$template = $isCompleted == 2 ? 'fetch_fail' : 'fetch_success';
			$taskHref = 'https://thei.org.ua/'.$jobs[$jobid]['lng'].'/task-set/view/'.$jobs[$jobid]['set_id'];
			// $taskHref = 'https://thei.org.ua/'.$jobs[$jobid]['lng'].'/task-submit/analyze?submit='.$jobs[$jobid]['id'];
			
			Mail::create()->send($jobs[$jobid]['uid'], $template, array(
				'jobid' => $jobid,
				'task_name' => TaskSubmit::getSubmitName($jobs[$jobid]['name'], $jobs[$jobid]['gridjob_name'], $jobs[$jobid]['index']),
				'task_status' => $allStatuses[$status]['title'],
				'task_href' => $taskHref,
			));
		}
	}
		
	$db->update('task_submits', $fields, 'jobid='.$db->qe($jobid));
	echo 'update '.$jobid.' with status "'.$data['nordugrid-job-status']."\"\n";
	
	// если задача только что получила свой первый статус
	if ($jobs[$jobid]['is_submitted'] == 1) {
		
		// запуск зависимых задач (если есть)
		if ($dependentTasksNum = $db->getCol('SELECT COUNT(1) FROM task_submit_queue WHERE trigger_task_id='.$jobs[$jobid]['id'])) {
			
			echo "starting ".$dependentTasksNum." dependent tasks, triggered by task #".$jobs[$jobid]['id']."\n";
			$script = FS_ROOT.'cron/submitter.php '.$jobs[$jobid]['id'];
			`php $script > /dev/null &`;
		}
			
		
	}
}


?>
