<?

define('TYPE_DIV', 'div');
define('TYPE_TABLE', 'table');

class CodeGenerator{
	
	
	public $_template = null;
	
	// КОНСТРУКТОР
	public function __construct($template, $clearDir = FALSE){
		
		$this->_template = $template;
		
		if(!$this->_template)
			throw new Exception('Неверное имя шаблона');
		
		if($clearDir)
			$this->clearOutputDir();
	}
	
	// ГЕНЕРАЦИЯ МОДЕЛИ
	public function generateModel($className, $tableName, $strValidatCommonRules, $strValidatIndividRules, $fieldTitles, $sortableFields){
		
		$placeholders = array(
			'__CLASSNAME__' 			=> $className,
			'__TABLENAME__' 			=> $tableName,
			'__VALIDATION_COMMON__' 	=> $strValidatCommonRules,
			'__VALIDATION_INDIVIDUAL__'	=> $strValidatIndividRules,
			'__FIELD_TITLES__' 			=> $fieldTitles,
			'__SORTABLE_FIELDS__'		=> $sortableFields,
		);
		$content = $this->parsePhpTemplate('templates/'.$this->_template.'/model.php', $placeholders);
		$this->createFile('output/models/', $className.'.model.php', $content);
	}

	// ГЕНЕРАЦИЯ КОНТРОЛЛЕРА
	public function generateController($controllerName, $modelName){
	
		$placeholders = array(
			'__CONTROLLERNAME__' => $controllerName,
			'__MODELNAME__' 	 => $modelName,
			'__MODEL_NAME_LOW__' => $this->getModelUrlPart($modelName),
		);
		$content = $this->parsePhpTemplate('templates/'.$this->_template.'/controller.php', $placeholders);
		$this->createFile('output/controllers/', str_replace('Controller', '', $controllerName).'.controller.php', $content);
	}
	
	// ГЕНЕРАЦИЯ ШАБЛОНА ADMIN-LIST
	public function generateTplAdminList($modelName, $fieldtitles, $sortableFields){
		// echo '<pre>'; var_dump($sortableFields); die;
		$content = $this->parseHtmlTemplate('templates/'.$this->_template.'/admin_list.tpl', array(
			'MODEL_NAME_LOW' => $this->getModelUrlPart($modelName),
			'FIELDS_TITLES' => $fieldtitles,
			'SORTABLE_FIELDS' => $sortableFields,
		));
		$this->createFile('output/templates/'.$modelName.'/', 'admin_list.tpl', $content);
	}
	
	// ГЕНЕРАЦИЯ ШАБЛОНА LIST
	public function generateTplList($modelName, $fieldtitles, $type = TYPE_TABLE){
		
		$tpl = $type == TYPE_TABLE ? 'list_table.tpl' : 'list_div.tpl';
		
		$content = $this->parseHtmlTemplate('templates/'.$this->_template.'/'.$tpl, array(
			'MODEL_NAME_LOW' => $this->getModelUrlPart($modelName),
			'FIELDS_TITLES' => $fieldtitles,
		));
		$this->createFile('output/templates/'.$modelName.'/', 'list.tpl', $content);
	}
	
	// ГЕНЕРАЦИЯ ШАБЛОНА VIEW
	public function generateTplView($modelName, $fieldtitles, $type = TYPE_TABLE){
	
		$tpl = $type == TYPE_TABLE ? 'view_table.tpl' : 'view_div.tpl';
		
		$content = $this->parseHtmlTemplate('templates/'.$this->_template.'/'.$tpl, array(
			'MODEL_NAME_LOW' => $this->getModelUrlPart($modelName),
			'FIELDS_TITLES' => $fieldtitles,
		));
		$this->createFile('output/templates/'.$modelName.'/', 'view.tpl', $content);
	}
	
	// ГЕНЕРАЦИЯ ШАБЛОНА EDIT
	public function generateTplEdit($modelName, $fieldtitles, $type = TYPE_TABLE){
	
		$tpl = $type == TYPE_TABLE ? 'edit_table.tpl' : 'edit_div.tpl';
		
		$content = $this->parseHtmlTemplate('templates/'.$this->_template.'/'.$tpl, array(
			'MODEL_NAME_LOW' => $this->getModelUrlPart($modelName),
			'FIELDS_TITLES' => $fieldtitles,
		));
		$this->createFile('output/templates/'.$modelName.'/', 'edit.tpl', $content);
	}
	
	// ГЕНЕРАЦИЯ ШАБЛОНА DELETE
	public function generateTplDelete($modelName, $fieldtitles){
		
		$content = $this->parseHtmlTemplate('templates/'.$this->_template.'/delete.tpl', array(
			'MODEL_NAME_LOW' => $this->getModelUrlPart($modelName),
			'FIELDS_TITLES' => $fieldtitles,
		));
		$this->createFile('output/templates/'.$modelName.'/', 'delete.tpl', $content);
	}
	
	// ФУНКЦИЯ СОЗДАНИЯ ФАЙЛА
	public function createFile($path, $file, $content){
	
		if(!is_dir($path))
			mkdir($path, 0777, true);
			
		$f = fopen($path.$file, 'w') or die('Невозможно открыть файл для сохранения модели');
		fwrite($f, $content) or die('Невозможно произвести запись в файл для сохранения модели');
		fclose($f);
	}
	
	// ФУНКЦИЯ ОЧИСТКИ ДИРЕКТОРИИ
	public function clearOutputDir(){
		
		if(!defined('FS_ROOT'))
			die('FS_ROOT not defined');
			
		$MODELS = FS_ROOT.'output/models';
		$CONTROLLERS = FS_ROOT.'output/controllers';
		$TEMPLATES = FS_ROOT.'output/templates';
		
		if(is_dir($MODELS)){
			self::_removeRecursive($MODELS);
		}
		
		if(is_dir($CONTROLLERS)){
			self::_removeRecursive($CONTROLLERS);
		}
		
		if(is_dir($TEMPLATES)){
			self::_removeRecursive($TEMPLATES);
		}
		
		Messenger::get()->addInfo('Ранее сгенерированные файлы удалены');
	}
	
	public static function _removeRecursive($fileOrDir){
	
		if(is_dir($fileOrDir)){
		
			foreach(scandir($fileOrDir) as $f){
				if($f != '.' && $f != '..'){
					if(is_dir($fileOrDir.'/'.$f)){
						self::_removeRecursive($fileOrDir.'/'.$f);
						@rmdir($fileOrDir.'/'.$f);
					}else{
						@unlink($fileOrDir.'/'.$f);
					}
				}
			}
			
			@rmdir($fileOrDir);
			
		}else{
			@unlink($fileOrDir);
		}
	}
	
	// ПАРСИТЬ PHP ШАБЛОН
	public function parsePhpTemplate($tpl, $placeholders){
		
		$output = '';
		foreach(file($tpl) as $row)
			if(substr($row, 0, 3) != '%%%')
				$output .= strtr($row, $placeholders);
		
		return $output;
	}
	
	// ПАРСИТЬ HTML ШАБЛОН
	public function parseHtmlTemplate($tpl, $variables){
		
		ob_start();
		extract($variables);
		include($tpl);
		return ob_get_clean();
	}
	
	public function getModelUrlPart($model){
	
		return strtolower(preg_replace('/([^\s])([A-Z])/', '\1-\2', $model));
	}
	
}

?>