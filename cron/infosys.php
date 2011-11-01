<?

error_reporting(E_ALL);
ini_set('display_errors', 1);

// обозначение корня ресурса
define('FS_ROOT', realpath('..').DIRECTORY_SEPARATOR);

define('TASK_TABLE', 'task_submits');
// define('TASK_TABLE', 'tasks');

// отправка Content-type заголовка
header('Content-Type: text/plain; charset=utf-8');

require_once('setup.php');
require_once(FS_ROOT.'includes/infosys/infosys.php');

$db = db::get();
$jobs = $db->getColIndexed('SELECT id, jobid FROM '.TASK_TABLE.' WHERE jobid IS NOT NULL AND LENGTH(jobid) > 0 ORDER BY id');

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
$statuses = $query->query_ARCJobs($jobs, $attrs);

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
	
	$db->update(TASK_TABLE, array(
			'is_submitted' => TRUE,
			'is_completed' => $isCompleted,
			'status'       => $allStatuses[$status]
		),
		'jobid='.$db->qe($jobid)
	);
	echo 'update '.$jobid.' with status "'.$data['nordugrid-job-status']."\"\n";
}


?>