<?

$ret;
for ($i = 0; $i < 1; $i++){
	$str = "&DUMP RENDER_FILE='forest_3.ge1', COLUMN_DUMP_LIMIT=.FALSE., DT_RESTART=300.00/";
	$res;
	preg_match("/&([A-z0-9_]+)\s+(.*)\//", $str, $res);//(([A-z0-9_]+)=\'?([A-z0-9_]+)\'?,\s)
	echo '<plaintext>';
	print_r($res);
	if (isset($res[1])) $ret[$i]['name'] = $res[1];
	if (isset($res[2])){
		$str = $res[2];
		preg_match_all("/(?:(?:([\w_]+)=(?:([^,]*)))+)(?:,\s*)?/", $str, $res);
		print_r($res);
		for ($j = 0; $j < count($res[1]); $j++){
			$ret[$i]['items'][$res[1][$j]] = $res[2][$j];
		}
	}
}
print_r($ret);

?>
