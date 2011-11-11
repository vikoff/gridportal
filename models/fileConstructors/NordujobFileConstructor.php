<?php

// запоминаем редактируемую строку и редактируемый фрагмент текста чтобы потом туда подставить мультиплаер

class NordujobFileConstructor extends AbstractFileConstructor {

	
	protected function _getFormRow($row, $rowIndex){
		
		$row = trim($row);
		
		// пропускаем комментарии
		if (preg_match('/^\(\*.*\*\)$/', $row))
			return null;
			
		if (preg_match('/^\((.+)="?([^"]+)"?\)$/', $row, $matches)) {
			$field = $matches[1];
			$value = $matches[2];
			return array(
				'row' => $rowIndex,
				'field' => $field,
				'value' => $value,
				'allow_multiple' => is_numeric(trim($value)),
			);
		} else {
			return null;
		}
	}
}

?>