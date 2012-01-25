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
	
	/**
	 * ПОЛУЧИТЬ МАССИВ ДАННЫХ ДЛЯ ОДНОЙ СТРОКИ ДЛЯ ФОРМЫ-КОНСТРУКТОРА
	 * @param string $row - строка из файла
	 * @param integer $rowIndex - номер текущей строки
	 * @return array|null - массив с ключами 
	 *                      'row'            - номер строки,
	 *                      'field'          - имя поля,
	 *                      'pre_text'       - текст, предшествующий строке значения,
	 *                      'value'          - строка значения,
	 *                      'post_text'      - текст, идущий после строки значения,
	 *                      'allow_multiple' - флаг, можно ли использовать множители
	 *                      или NULL, если строка не должна редактироваться в форме
	 */
	protected function _getFormRow($row, $rowIndex){
		
		$row = trim($row);
		
		// пропускаем комментарии
		if (preg_match('/^\(\*.*\*\)$/', $row))
			return null;
			
		if (preg_match('/^(\((.+)=\s*"?)(.+?)("?\s*\))$/', $row, $matches)) {
			//            (----pre-----)(value)(-post--)
			// echo '<pre>'; print_r($matches); echo '</pre>';
			$field = $matches[2];
			$value = $this->_getFormValueFromFileValue(trim($matches[3]));
			
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