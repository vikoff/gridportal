<?

// обозначение текущий папки
define('CUR_PATH', dirname(__FILE__).'/');

// обозначение корня ресурса
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);

require(CUR_PATH.'setup.php');
require(FS_ROOT.'setup.php');

// забираем задачи, которые выполнились более шести часов назад
$startDate = time() + 3600 * 6;

echo "check tasks for fetch\n";

$db = db::get();
$taskIds = $db->getAll('
	SELECT id FROM '.TaskSubmit::TABLE.'
	WHERE is_completed=1 AND is_fetched=0 AND finish_date>'.$startDate.'
	ORDER BY id');

if (empty($taskIds))
	echo "no tasks for fetch found\n";

foreach ($taskIds as $id) {
	
	logMsg('start task #'.$submitInstance->id.' fetching', 'get-submits');
	$submitInstance = TaskSubmit::load($id);
	$connector = getMyproxyConnector($submitInstance->getUid());
	
	$submitInstance->getResults($connector);
	logMsg('task #'.$submitInstance->id.' fetched', 'get-submits');
}
?>
