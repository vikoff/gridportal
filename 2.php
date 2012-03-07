<?

$pattern = '/^\d*,\d+$/';
$string = array();
$string[] = '1';
$string[] = '10';
$string[] = '10.1';
$string[] = '10,1';
$string[] = 'ololo';
$string[] = '.';
$string[] = '.5';
$string[] = ',5';

echo '<pre>';
foreach ($string as $s)
	echo $s.' '.(preg_match($pattern, $s) ? 'match' : 'not').'<br />';

?>