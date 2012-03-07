<?

error_reporting(E_ALL);
header("Content-Type: text/plain; charset=urf-8");

$model_data = file_get_contents("forest_3_1_1.fds"); //"&DUMP RENDER_FILE='forest_3.ge1', COLUMN_DUMP_LIMIT=.FALSE., DT_RESTART=300.00/";
$model = array();

$model_data = preg_replace("/,\s*\n\s*/", ", ", $model_data);
preg_match_all('/&.+\/.*\n/', $model_data, $model);

//print_r($model);
//die;

$ret = array();
for ($i = 0; $i < count($model[0]); $i++){
	
	$str = $model[0][$i];
	$res = array();
	preg_match("/&([A-z0-9_]+)\s+(.*)\//", $str, $res);//(([A-z0-9_]+)=\'?([A-z0-9_]+)\'?,\s)
	//print_r($res);
	
	if (isset($res[1])) $ret[$i]['name'] = $res[1];
	if (isset($res[2])){
		$str = $res[2];
		//preg_match_all("/(?:(?:([\w_]+)=(?:([^,]*)))+)(?:,\s*)?/", $str, $res);
		preg_match_all("/(?:(?:([^,\s]+)=(?:([^,]*)))+)(?:,\s*)?/", $str, $res);
		//print_r($res);
		for ($j = 0; $j < count($res[1]); $j++){
			//$res[1][$j] = preg_replace("/[\W]/", "_", $res[1][$j]);
			$ret[$i]['args'][$res[1][$j]] = $res[2][$j];
		}
	}
}

print_r($ret);
/*
foreach ($ret as $i => $v){
	//print_r($v);
	if (isset($v['args']['ID']) && $v['args']['ID'] == "'кроны дерева03'") print_r($v);
}*/

?>
