<?php

/**
 * ЗАДАЧИ, РЕШАЕМЫЕ КЛАССОМ
 * 1) сгенерировать форму мастера [ self::getConstructorHTML() ]
 * 2) размножить файл для сабмитов
 * 3) вывести статистику (количество вариаций, и т.д.)
 */

abstract class AbstractFileConstructor {
	
	public $filename = null;
	
	public $multiplierLeftTag = '{*';
	public $multiplierRightTag = '*}';
	
	protected $_fileContent = '';
	
	
	public function __construct($filename){
	
		$this->filename = $filename;
	}
	
	public function getConstructorFormData(){
		
		$formRows = array();
		foreach(file($this->filename) as $index => $row)
			if ($formRow = $this->_getFormRow($row, $index))
				$formRows[] = $formRow;
		
		return $formRows;
	}
	
	abstract protected function _getFormRow($row, $rowIndex);
}

?>