<?php

class FdsFileConstructor extends AbstractFileConstructor {

	
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
		
		$field         = null;
		$preText       = null;
		$value         = null;
		$postText      = null;
		$allowMultiple = null;
		
		// TIME T_END
		if (preg_match('/(&TIME T_END=)(.*)(\/)/', $row, $matches)) {
			// echo '<pre>'; print_r($matches); die;
			$field         = 'TIME T_END';
			// $field         = Lng::get('file-constructors.fds.T_END');
			$preText       = $matches[1];
			$value         = $matches[2];
			$postText      = $matches[3];
		}
		
		// MISC TMPA
		elseif (preg_match('/(&MISC TMPA=)(.*)(\/)/', $row, $matches)) {
			// echo '<pre>'; print_r($matches); die;
			$field         = 'MISC TMPA';
			// $field         = Lng::get('file-constructors.fds.T_END');
			$preText       = $matches[1];
			$value         = $matches[2];
			$postText      = $matches[3];
		}
		
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
}

?>