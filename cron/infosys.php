<?

error_reporting(E_ALL);
ini_set('display_errors', 1);

// обозначение корня ресурса
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);

define('TASK_TABLE', 'task_submits');
define('TASK_QUEUE_TABLE', 'task_submit_queue');
// define('TASK_TABLE', 'tasks');

// отправка Content-type заголовка
header('Content-Type: text/plain; charset=utf-8');

require_once('setup.php');
require_once(FS_ROOT.'includes/infosys/infosys.php');

$db = db::get();
$jobs = $db->getAllIndexed('
	SELECT id, jobid, is_submitted FROM '.TASK_TABLE.'
	WHERE 
		jobid IS NOT NULL AND LENGTH(jobid) > 0 AND
		is_submitted > 0 AND
		is_completed = 0
	ORDER BY id
', 'jobid');

echo "TASKS ".print_r($jobs, 1)."\n";

if(empty($jobs))
	die('no jobs');
	
$allStatuses = $db->getColIndexed('SELECT name, id FROM task_states');
	
$query = new BDIIQuery_ARCJobs(array( 
	"server" => "bdii.grid.org.ua",
	"port" => 2170,
	"basedn" => "Mds-Vo-name=local,o=grid"
));

$attrs = array ( 'nordugrid-job-status');
$statuses = $query->query_ARCJobs(array_keys($jobs), $attrs);

echo "RESPONSE ".print_r($statuses, 1)."\n";

if(empty($statuses))
	die('no statuses received');

foreach($statuses as $jobid => $data){
	
	$status = $data['nordugrid-job-status'];
	if(!isset($allStatuses[$status]))
		$allStatuses[$status] = $db->insert('task_states', array('name' => $status, 'title' => 'task.state.'.strtolower($status)));
	
	$isCompleted = 0;
	switch($status){
		case 'FINISHED': $isCompleted = 1; break;
		case 'FAILED':   $isCompleted = 2; break;
		case 'DELETED':  $isCompleted = 3; break;
	}
	
	$fields = array(
		'is_submitted' => 2,
		'is_completed' => $isCompleted,
		'status'       => $allStatuses[$status]
	);
	
	if ($isCompleted) 
		$fields['finish_date'] = time();
		
	$db->update(TASK_TABLE, $fields, 'jobid='.$db->qe($jobid));
	echo 'update '.$jobid.' with status "'.$data['nordugrid-job-status']."\"\n";
	
	// если задача только что получила свой первый статус
	if ($jobs[$jobid]['is_submitted'] == 1) {
		
		// запуск зависимых задач (если есть)
		if ($dependentTasksNum = $db->getCol('SELECT COUNT(1) FROM '.TASK_QUEUE_TABLE.' WHERE trigger_task_id='.$jobs[$jobid]['id'])) {
			
			echo "starting ".$dependentTasksNum." dependent tasks, triggered by task #".$jobs[$jobid]['id']."\n";
			$script = FS_ROOT.'cron/submitter.php '.$jobs[$jobid]['id'];
			`php $script > /dev/null &`;
		}
			
		
	}
}


?>