<?php

class FdsFileConstructor extends AbstractFileConstructor {

	protected $paramRules = array(
		'*' => array(
			'allowed' => true,			// разрешаем отображение всех (*) параметров по умолчанию
			'args' => array(
				'*' => array(
					'allowed' => true	// разрешаем отображение всех (*) аттрибутов по умолчанию
				)
			)
		),
		'HEAD' => array(
			'allowed' => false
		),
		'TAIL' => array(
			'allowed' => false
		),
		'MATL' => array(
			'args' => array(
				'ID' => array(
					'allowed' => false
				)
			)
		),
		'MESH' => array(
			'allowed' => true,			// разрешаем отображение параметра MESH
			'args' => array(
				'IJK' => array(
					'allowed' => false	// запрещаем отображение аттрибута IJK параметра MESH
				)
			)
		),
		'OBST' => array(
			'allowed' => false,
		),
		'SURF' => array(
			'allowed' => false,
		),
		/*
		'PARAM' => array(				// PARAM - имя параметра (* - любой)
			'allowed' => true,			// разрешение на отображение (true/false)
			'args' => array(
				'ARG' => array(			// ARG - имя аттрибута (* - любой)
					'allowed' => false	// разрешение на отображение (true/false)
				)
			)
		)
		*/
	);
	
	/**
	 * ПОЛУЧИТЬ МАССИВ ДАННЫХ ДЛЯ ОДНОЙ СТРОКИ ДЛЯ ФОРМЫ-КОНСТРУКТОРА
	 * @param string $row - строка из файла
	 * @param integer $rowIndex - номер текущей строки
	 * @return array|null - массив с ключами 
	 *                      'index'          - индекс параметра в общем массиве параметров,
	 *                      'field'          - имя поля,
	 *                      'value'          - строка значения,
	 *                      'allow_multiple' - флаг, можно ли использовать множители
	 *                      или NULL, если строка не должна редактироваться в форме
	 */
	protected function _getFormRow($row, $rowIndex){
		
		$row = trim($row);
		
		$field         = null;
		$preText       = null;
		$value         = null;
		$postText      = null;
		$allowMultiple = null;
		
		// отлов множителей
		$value = $this->_getFormValueFromFileValue($value);
		$allowMultiple = is_array($value) || is_numeric(trim($value));
		
		if (0) {
			return array(
				'row'            => $rowIndex,
				'items' => array(
					'field'          => $field,
					'value'          => $value,
					'allow_multiple' => $allowMultiple,
				),
			);
		}
		
		if ($field) {
			return array(
				'row'            => $rowIndex,
				'field'          => $field,
				'pre_text'       => $preText,
				'value'          => $value,
				'post_text'      => $postText,
				'allow_multiple' => $allowMultiple,
			);
		} else {
			return null;
		}
	}
	
	public function getConstructorFormData(){
		
		$modelData = file_get_contents($this->filename);
		$model = array();
		$modelData = preg_replace("/,\s*\n\s*/", ", ", $modelData);
		preg_match_all('/&.+\/.*\n/', $modelData, $model);

		$ret = array();
		for ($i = 0; $i < count($model[0]); $i++){
			
			// строка, содержащая один параметр и его аргументы
			$str = $model[0][$i];
			$res = array();
			preg_match("/&([A-z0-9_]+)\s+(.*)\//", $str, $res);
			$name = $res[1];
			$args = $res[2];
			
			if (isset($res[1])){
				if (!$this->isAllowedParam($name)) continue;
				$ret[$i]['name'] = $name;
			
				if (isset($args)){
					$res = array();
					$tmpStr = '';
					$collecting = false;
					foreach (explode(', ', $args) as $attr) {
						// нечетное число кавычек
						$isOdd = substr_count($attr, "'") % 2;
						if ($collecting) {
							if ($isOdd) {
								$collecting = false;
								$res[] = $tmpStr.', '.$attr;
								$tmpStr = '';
							} else {
								$tmpStr .= ', '.$attr;
							}
						} else {
							if ($isOdd) {
								$collecting = true;
								$tmpStr = $attr;
							} else {
								$res[] = $attr;
							}
						}
					}
					$argsArray = $res;
					foreach ($argsArray as $val){
						$tmp = explode('=', $val);
						if (!isset($tmp[1])) continue;
						$arg = $tmp[0];
						$value = $tmp[1];
						if (!$this->isAllowedParamArg($name, $arg)) continue;
						$value = $this->_getFormValueFromFileValue($value);
						$ret[$i]['args'][$arg] = array(
							'value' => $value,
							'allow_multiple' => is_array($value) || is_numeric(trim($value)),
						);
					}
				}
			}
		}
		
		// exit;
		return $ret;
	}
	
		
	/** СОХРАНИТЬ ДАННЫЕ ИЗ ФОРМЫ-КОНСТРУКТОРА В ФАЙЛ */
	public function saveConstructorFormData($formData, $setInstance = null){
		
		$modelData = file_get_contents($this->filename);
		$modelData = str_replace("\r", "", $modelData);
		$fileWrap = array();
		preg_match("/(.*)&HEAD.*&TAIL\s\/\n(.*)/s", $modelData, $fileWrap);
		$model = array();
		$modelData = preg_replace("/,\s*\n\s*/", ", ", $modelData);
		preg_match_all('/&.+\/.*\n/', $modelData, $model);
		
		$ret = array();
		for ($i = 0; $i < count($model[0]); $i++){
			
			$str = $model[0][$i];
			$res = array();
			preg_match("/&([A-z0-9_]+)\s+(.*)\/\s(.*)/", $str, $res);
			$name = $res[1];
			$args = $res[2];
			$comment = $res[3];
			
			if (isset($res[1])){
				$ret[$i]['name'] = $name;
				$ret[$i]['comment'] = $comment;
			
				if (isset($args)){
					$res = array();
					$tmpStr = '';
					$collecting = false;
					foreach (explode(', ', $args) as $attr) {
						// нечетное число кавычек
						$isOdd = substr_count($attr, "'") % 2;
						if ($collecting) {
							if ($isOdd) {
								$collecting = false;
								$res[] = $tmpStr.', '.$attr;
								$tmpStr = '';
							} else {
								$tmpStr .= ', '.$attr;
							}
						} else {
							if ($isOdd) {
								$collecting = true;
								$tmpStr = $attr;
							} else {
								$res[] = $attr;
							}
						}
					}
					$argsArray = $res;
					foreach ($argsArray as $val){
						$tmp = explode('=', $val);
						if (!isset($tmp[1])) continue;
						$arg = $tmp[0];
						$value = $tmp[1];
						$value = $this->_getFormValueFromFileValue($value);
						$ret[$i]['args'][$arg] = array(
							'value' => $value,
							'allow_multiple' => is_array($value) || is_numeric(trim($value)),
						);
					}
				}
			}
		}
		
		foreach ($_POST['keys'] as $i => $a){
			foreach ($a as $k => $v){
				$ret[intval($i)]['args'][$k]['value'] = $this->parseFormMultiplier($_POST['items'][intval($v)]['value']);
			}
		}
		
		$content = "";
		if (isset($fileWrap[1])) $content .= $fileWrap[1];
		foreach ($ret as $item){
			$args = array();
			if (isset($item['args'])){
				foreach ($item['args'] as $argn => $argv){
					$args[] = $argn . '=' . $argv['value'];
				}
			}
			if (empty($item['comment'])) $content .= '&' . $item['name'] . ' ' . implode(', ', $args) . '/' . "\n";
			else $content .= '&' . $item['name'] . ' ' . implode(', ', $args) . '/ ' . $item['comment'] . "\n";
		}
		if (isset($fileWrap[2])) $content .= $fileWrap[2];
		
		file_put_contents($this->filename, $content);
	}
	
	protected function isAllowedParam($param){
		if (isset($this->paramRules[$param]['allowed'])) return (bool)$this->paramRules[$param]['allowed'];
		else return !empty($this->paramRules['*']['allowed']);
	}
	
	protected function isAllowedParamArg($param, $arg){
		if (isset($this->paramRules[$param]['args'][$arg]['allowed'])) return (bool)$this->paramRules[$param]['args'][$arg]['allowed'];
		elseif (isset($this->paramRules['*']['args'][$arg]['allowed'])) return (bool)$this->paramRules[$param]['args'][$arg]['allowed'];
		else return !empty($this->paramRules['*']['args']['*']['allowed']);
	}
	
}

?>