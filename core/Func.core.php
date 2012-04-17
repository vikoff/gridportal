<?php

// ФУНКЦИЯ AUTOLOAD
function __autoload($className){
	
	// индекс всех элементов из CORE (кроме App и Func),
	// а так же всех компонентов.
	static $filesIndex = array(
		'Common' 			=> 'core/Common.core.php',
		'db' 				=> 'core/Db.core.php',
		'DbAdapter_mysql'	=> 'core/DbAdapters/mysql.php',
		'DbAdapter_sqlite'	=> 'core/DbAdapters/sqlite.php',
		'CommonViewer' 		=> 'core/CommonViewer.core.php',
		'Controller' 		=> 'core/Controller.core.php',
		'Error' 			=> 'core/Error.core.php',
		'GenericObject' 	=> 'core/GenericObject.core.php',
		'GenericObjectCollection' => 'core/GenericObject.core.php',
		'ImageMaster'		=> 'core/ImageMaster.core.php',
		'Messenger' 		=> 'core/Messenger.core.php',
		'Paginator' 		=> 'core/Paginator.core.php',
		'UserStatistics'	=> 'core/UserStatistics.core.php',
		'UserStatisticsCollection' => 'core/UserStatistics.core.php',
		'Validator' 		=> 'core/Validator.core.php',
		'YDate' 			=> 'core/YDate.core.php',
		'CsvParser' 		=> 'core/CsvParser.core.php',
		'YArray' 			=> 'core/YArray.core.php',
		'Sorter'			=> 'core/Sorter.core.php',
		'Exception403'		=> 'core/Exception.core.php',
		'Exception404'		=> 'core/Exception.core.php',
		'FormBuilder'		=> 'core/FormBuilder.core.php',
		'Tools'				=> 'core/Tools.core.php',
		
		'AjaxViewer'		=> 'components/AjaxViewer.component.php',
		'BackendViewer'		=> 'components/BackendViewer.component.php',
		'Def'				=> 'components/Def.component.php',
		'FrontendViewer'	=> 'components/FrontendViewer.component.php',
		'Request' 			=> 'components/Request.component.php',
		'User' 				=> 'components/User.component.php',
		'UserCollection'	=> 'components/User.component.php',
		'CurUser' 			=> 'components/CurUser.component.php',
	);
	
	// поиск по индексу
	if(isset($filesIndex[$className])){
		require(FS_ROOT.$filesIndex[$className]);
		return;
	}
	
	// контроллер
	if(strpos($className, 'Controller')){
	
		$fname = FS_ROOT.'controllers/'.str_replace('Controller', '.controller.php', $className);
		if(file_exists($fname))
			require($fname);
		return;
	}
	
	else{
		
		// модель
		$fileName = FS_ROOT.'models/'.$className.'.model.php';
		if(file_exists($fileName)){
			require($fileName);
			return;
		}
		
		// коллекция
		$fileName = FS_ROOT.'models/'.str_replace('Collection', '', $className).'.model.php';
		if(file_exists($fileName)){
			require($fileName);
			return;
		}
	}
	
}

// ФУНКЦИЯ GETVAR
function getVar(&$varname, $defaultVal = '', $type = ''){

	if(!isset($varname))
		return $defaultVal;
	
	if(strlen($type))
		settype($varname, $type);
	
	return $varname;
}

function href($href, $lng = null){
		
	$href = ($lng ? $lng : Lng::get()->getCurLng()).'/'.$href;
	return WWW_ROOT.(CFG_USE_SEF
		? $href											// http://site.com/controller/method?param=value
		: 'index.php?r='.str_replace('?', '&', $href));	// http://site.com/index.php?r=controller/method&param=value
}

function formatHumanReadableSize($size) {
	$units = array('B', 'KB', 'MB', 'GB', 'TB');
	for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
	return round($size, 2).' '.Lng::get('filesize.' . $units[$i]);
}

if (!function_exists('json_encode')) {
	
	function json_encode($data) {
		switch ($type = gettype($data)) {
			case 'NULL':
				return 'null';
			case 'boolean':
				return ($data ? 'true' : 'false');
			case 'integer':
			case 'double':
			case 'float':
				return $data;
			case 'string':
				return '"' . addslashes($data) . '"';
			case 'object':
				$data = get_object_vars($data);
			case 'array':
				$output_index_count = 0;
				$output_indexed = array();
				$output_associative = array();
				foreach ($data as $key => $value) {
					$output_indexed[] = json_encode($value);
					$output_associative[] = json_encode($key) . ':' . json_encode($value);
					if ($output_index_count !== NULL && $output_index_count++ !== $key) {
						$output_index_count = NULL;
					}
				}
				if ($output_index_count !== NULL) {
					return '[' . implode(',', $output_indexed) . ']';
				} else {
					return '{' . implode(',', $output_associative) . '}';
				}
			default:
				return ''; // Not supported
		}
	}
	
} 