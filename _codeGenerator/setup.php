<?

function getVar(&$varname, $defaultVal = '', $type = ''){

	if(!isset($varname))
		return $defaultVal;
	
	if(strlen($type))
		settype($varname, $type);
	
	return $varname;
}

function getHtmlTempateTypesList($selected, $items = 'dte'){
	return ''
		.(strpos($items, 'd') !== FALSE ? '<option value="div" '.($selected == 'div' ? 'selected="selected"' : '').'>div</option>' : '')
		.(strpos($items, 't') !== FALSE ? '<option value="table" '.($selected == 'table' ? 'selected="selected"' : '').'>table</option>' : '')
		.(strpos($items, 'e') !== FALSE ? '<option value="" '.($selected == '' ? 'selected="selected"' : '').'>disable</option>' : '')
	;
}

function reload(){
	
	$url = Messenger::get()->qsAppendFutureKey('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	header('location: '.$url);
	exit();
}

require_once('data/CodeGenerator.class.php');
require_once('data/DbStuctParser.class.php');
require_once('data/Messenger.class.php');
require_once('data/Storage.class.php');

$s = & Storage::get()->data;

?>