<?

$action = isset($_POST['action']) ? $_POST['action'] : '';

$messenger = Messenger::get();

// СОХРАНЕНИЕ ДАННЫХ
if($action == 'saveData'){
	
	if(get_magic_quotes_gpc() || get_magic_quotes_runtime())
		foreach($_POST as &$p)
			$p = stripslashes($p);
		
	$s['template'] = trim($_POST['template']);
	
	$s['tablename'] = trim($_POST['tablename']);
	$s['modelclass'] = trim($_POST['modelclass']);
	$s['controlclass'] = trim($_POST['controlclass']);
	
	$s['strValidatCommonRules'] = trim($_POST['validatCommonRules']);
	$s['strValidatIndividRules'] = trim($_POST['validatIndividRules']);
	
	$s['strFieldsTitles'] = trim($_POST['fieldsTitles']);
	if(strlen(trim($s['strFieldsTitles'])))
		eval('$s[\'fieldsTitles\'] = '.$s['strFieldsTitles'].';');
	
	$s['strSortableFields'] = trim($_POST['sortableFields']);
	$s['sortableFields'] = strlen($s['strSortableFields'])
		? explode(' ', preg_replace('/\s+/m', ' ', trim($s['strSortableFields'])))
		: array();
	
	
	Storage::get()->save();
	
	$messenger->addSuccess('Данные сохранены');
	reload();
}

// СГЕНЕРИРОВАТЬ ФАЙЛЫ
elseif($action == 'generate'){
	
	$s['files']['model'] 			= getVar($_POST['files']['model']);
	$s['files']['controller'] 		= getVar($_POST['files']['controller']);
	$s['files']['tpl-admin-list']	= getVar($_POST['files']['tpl-admin-list']);
	$s['files']['tpl-list'] 		= getVar($_POST['files']['tpl-list']);
	$s['files']['tpl-view']		 	= getVar($_POST['files']['tpl-view']);
	$s['files']['tpl-edit'] 		= getVar($_POST['files']['tpl-edit']);
	$s['files']['tpl-delete'] 		= getVar($_POST['files']['tpl-delete']);
	
	$s['clear-output-dir']			= getVar($_POST['clear-output-dir'], FALSE, 'bool');
	
	Storage::get()->save();
	
	$generator = new CodeGenerator(getVar($s['template']), $s['clear-output-dir']);
	
	$successMsg = '';
	
	try{
		
		// сгенерировать модель
		if(!empty($s['files']['model'])){
			
			$sortableFields = "\n";
			foreach($s['sortableFields'] as $f)
				$sortableFields .= "\t\t'".$f."' => '".(isset($s['fieldsTitles'][$f]) ? $s['fieldsTitles'][$f] : $f)."',\n";

			$generator->generateModel(
				$s['modelclass'],
				$s['tablename'],
				$s['strValidatCommonRules'],
				$s['strValidatIndividRules'],
				$s['strFieldsTitles'],
				$sortableFields
			);
			$successMsg .= '<p>Файл модели сохранен!</p>';
		}

		// сгенерировать контроллер
		if(!empty($s['files']['controller'])){
		
			$generator->generateController(
				$s['controlclass'],
				$s['modelclass']
			);
			$successMsg .= '<p>Файл контроллера сохранен!</p>';
		}

		// сгенерировать шаблон admin-list
		if(!empty($s['files']['tpl-admin-list'])){

			$generator->generateTplAdminList(
				$s['modelclass'],
				$s['fieldsTitles'],
				$s['sortableFields']
			);
			$successMsg .= '<p>Файл Шаблона admin-list сохранен!</p>';
		}

		// сгенерировать шаблон list
		if(!empty($s['files']['tpl-list'])){

			$generator->generateTplList(
				$s['modelclass'],
				$s['fieldsTitles'],
				$s['files']['tpl-list']
			);
			$successMsg .= '<p>Файл Шаблона list сохранен!</p>';
		}

		// сгенерировать шаблон view
		if(!empty($s['files']['tpl-view'])){

			$generator->generateTplView(
				$s['modelclass'],
				$s['fieldsTitles'],
				$s['files']['tpl-view']
			);
			$successMsg .= '<p>Файл Шаблона view сохранен!</p>';
		}

		// сгенерировать шаблон edit
		if(!empty($s['files']['tpl-edit'])){

			$generator->generateTplEdit(
				$s['modelclass'],
				$s['fieldsTitles'],
				$s['files']['tpl-edit']
			);
			$successMsg .= '<p>Файл Шаблона edit сохранен!</p>';
		}

		// сгенерировать шаблон delete
		if(!empty($s['files']['tpl-delete'])){

			$generator->generateTplDelete(
				$s['modelclass'],
				$s['fieldsTitles']
			);
			$successMsg .= '<p>Файл Шаблона delete сохранен!</p>';
		}
		
		if($successMsg)
			$messenger->addSuccess($successMsg);
	
	}catch(Exception $e){
		
		if($successMsg)
			$messenger->addSuccess($successMsg);
		
		$messenger->addError('При генерации файлов произошли ошибки:<br />'.$e->getMessage());
	
	}
		
	reload();
	
}

// ПАРСИНГ CREATE TABLE СТРОКИ
elseif($action == 'db-parse-create'){
	
	$error = '';
	
	$str = trim($_POST['create-table-str']);
	if(!strlen($str))
		$error .= 'Получена пустая строка';
		
	if(get_magic_quotes_gpc() || get_magic_quotes_runtime())
		$str = stripslashes($str);
	
	if(!$error){
		try{
			$parser = new DbStructParser($str, DbStructParser::SRC_CREATE);
			
			$s['tableStruct'] = $parser->getStructure();
			$s['validatCommonRules'] = $parser->getCommonRules();
			$s['validatIndividRules'] = $parser->getIndividualRules();
			
			if(!getVar($s['tablename']) && !is_null($parser->getTableName()))
				$s['tablename'] = $parser->getTableName();

			Storage::get()->save();
			
		}catch(Exception $e){
			$error .= $e->getMessage();
		}
	}
	
	if($error){
		$messenger->addError($error);
	}else{
		?>
		<script type="text/javascript">
			window.opener.window.location.reload();
			window.location.href='?action=db-eval-describe';
		</script>	
		<?
	}
}

// ПАРСИНГ DESCRIBE СТРОКИ
elseif($action == 'db-eval-describe'){
	
	$error = '';
	
	$str = trim($_POST['describe-str']);
	if(!strlen($str))
		$error .= 'Получена пустая строка';
		
	if(get_magic_quotes_gpc() || get_magic_quotes_runtime())
		$str = stripslashes($str);
	
	if(!$error){
		try{
			$parser = new DbStructParser($str, DbStructParser::SRC_DESCRIBE_EVAL);
			$s['tableStruct'] = $parser->getStructure();
			$s['validatCommonRules'] = $parser->getCommonRules();
			$s['validatIndividRules'] = $parser->getIndividualRules();

			Storage::get()->save();
			
		}catch(Exception $e){
			$error .= $e->getMessage();
		}
	}
	
	if($error){
		$messenger->addError($error);
	}else{
		?>
		<script type="text/javascript">
			window.opener.window.location.reload();
			window.close();
		</script>	
		<?
	}
}

// ОЧИСТКА СЕССИИ
elseif($action == 'clearSession'){

	$s = array();
	Storage::get()->save();
	
	$messenger->addInfo('Все данные очищены');
	reload();
}



?>