<?php

// запоминаем редактируемую строку и редактируемый фрагмент текста чтобы потом туда подставить мультиплаер

class NordujobFileConstructor extends AbstractFileConstructor {

	
	protected function _getFormRow($row, $rowIndex){
		
		$row = trim($row);
		
		// пропускаем комментарии
		if (preg_match('/^\(\*.*\*\)$/', $row))
			return null;
			
		if (preg_match('/^(\((.+)="?)([^"]+)("?\))$/', $row, $matches)) {
			//            (---------)(-----)(----)
			// echo '<pre>'; print_r($matches); echo '</pre>';
			$field = $matches[2];
			$value = $matches[3];
			return array(
				'row' => $rowIndex,
				'field' => $field,
				'pre_text' => $matches[1],
				'value' => $value,
				'post_text' => $matches[4],
				'allow_multiple' => is_numeric(trim($value)),
			);
		} else {
			return null;
		}
	}
}

?>