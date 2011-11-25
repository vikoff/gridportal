<?php

/**
 * ЗАДАЧИ, РЕШАЕМЫЕ КЛАССОМ
 * 1) сгенерировать форму мастера [ self::getConstructorHTML() ]
 * 2) размножить файл для сабмитов
 * 3) вывести статистику (количество вариаций, и т.д.)
 */

abstract class AbstractFileConstructor {
	
	public $filename = null;
	public $basename = null;
	
	public $multiplierLeftTag = '{*';
	public $multiplierRightTag = '*}';
	
	protected $_fileContent = '';
	
	
	public function __construct($filename){
	
		$this->filename = $filename;
		$this->basename = basename($this->filename);
	}
	
	public function getConstructorFormData(){
		
		$formRows = array();
		foreach(file($this->filename) as $index => $row)
			if ($formRow = $this->_getFormRow($row, $index))
				$formRows[] = $formRow;
		
		return $formRows;
	}
	
	public function getMultipliers(){
		
		$multipliers = array();
		foreach (file($this->filename) as $index => $row) {
			if (preg_match_all('/\{\*(.+?)\*\}/', $row, $matches)) {
				foreach ($matches[1] as $mult) {
					$multipliers[] = array(
						'file' => $this->basename,
						'row' => $index,
						'valuesStr' => $mult,
						'values' => $this->parseStrMultiplier($mult),
					);
				}
			}
		}
		
		return $multipliers;
	}
	
	public function getCombination($values){
		$fileArr = file($this->filename);
		foreach ($values as $v) {
			if (isset($fileArr[ $v['row'] ]))
				$fileArr[ $v['row'] ] = str_replace(
					$this->multiplierLeftTag.$v['valuesStr'].$this->multiplierRightTag,
					$v['value'],
					$fileArr[ $v['row'] ]
				);
			else
				throw new Exception('Файл '.$this->filename.' не содержит строки #'.$v['row']);
		}
		
		return implode("", $fileArr)."\n";
	}
	
	public function parseStrMultiplier($str){
		
		$values = explode(',', $str);
		foreach ($values as &$v) {
			// интервал
			if (strpos($v, ';') !== FALSE) {
				list($intervalStr, $step) = explode(':', $v) + array('', 0); // отделение шага
				$interval = explode(';', $intervalStr) + array(0, 0);
				$v = array('from' => $interval[0], 'to' => $interval[1], 'step' => $step);
			}
			// одиночное значение
			else {
				$v = trim($v);
			}
		}
		return $values;
	}
	
	abstract protected function _getFormRow($row, $rowIndex);
}

?>