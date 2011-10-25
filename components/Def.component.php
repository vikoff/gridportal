<?

class Def{

	$noteStates = array(
		'0' => array('name' => 'notStarted', 'title' => 'Еще не начинал'),
		'1' => array('name' => 'justStarted', 'title' => 'Только начал'),
		'2' => array('name' => 'inProcess', 'title' => 'В процессе'),
		'3' => array('name' => 'almostComplete', 'title' => 'Почти готово'),
		'4' => array('name' => 'complete', 'title' => 'Готово'),
	);
	
	$noteUrgency = array(
		'1' => array('name' => 'high', 'title' => 'Высокая срочность'),
		'2' => array('name' => 'mid', 'title' => 'Средняя срочность'),
		'3' => array('name' => 'low', 'title' => 'Низкая срочность'),
	);

}

?>