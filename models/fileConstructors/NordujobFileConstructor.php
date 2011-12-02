<?php

class NordujobFileConstructor extends AbstractFileConstructor {

	public function saveConstructorFormData($formData, $setInstance = null){
		
		// echo '<pre>'; print_r($formData); die;
		// обновление поля gridjob_name в таблице task_sets
		foreach($formData as $row) {
			if (trim($row['field']) == 'jobname') {
				$setInstance->setField('gridjob_name', trim($row['value']));
				$setInstance->_save();
				break;
			}
		}
		parent::saveConstructorFormData($formData, $setInstance);
	}
	
	protected function _getFormRow($row, $rowIndex){
		
		$row = trim($row);
		
		// пропускаем комментарии
		if (preg_match('/^\(\*.*\*\)$/', $row))
			return null;
			
		if (preg_match('/^(\((.+)=\s*"?)(.+?)("?\s*\))$/', $row, $matches)) {
			//            (----pre-----)(value)(-post--)
			// echo '<pre>'; print_r($matches); echo '</pre>';
			$field = $matches[2];
			$value = trim($matches[3]);
			
			// отлавливаем множители
			if (preg_match('/^\{\*(.+)\*\}$/', $value, $submatches))
				$value = $this->parseStrMultiplier($submatches[1]);
			
			return array(
				'row' => $rowIndex,
				'field' => $field,
				'pre_text' => $matches[1],
				'value' => $value,
				'post_text' => $matches[4],
				'allow_multiple' => is_array($value) || is_numeric(trim($value)),
			);
		} else {
			// echo '<pre>NO MATCH! '; print_r($row); echo '</pre>';
			return null;
		}
	}
}

?>