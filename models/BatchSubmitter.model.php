<?

class BatchSubmitter {
	
	public $multipliers = array();
	public $numMultiplayers = 0;
	public $numCombinations = 1;
	public $curCombination = 0;
	
	
	public function __construct(){
		
	}
	
	public function addMultiplier($file, $row, $valuesArr, $valuesStr){
		
		$numValues = count($valuesArr);
		if (!$numValues)
			throw new Exception('Множитель в файле <b>'.$file.'</b> (строка '.($row + 1).') не имеет ни одного значения.');
		
		if (!isset($this->multipliers[$file]))
			$this->multipliers[$file] = array();
			
		$this->multipliers[$file][] = array(
			'row'		=> $row,
			'valuesArr' => $valuesArr,
			'valuesStr' => $valuesStr,
			'curIndex'  => 0,
			'maxIndex'  => $numValues - 1,
		);
		$this->numMultiplayers++;
		$this->numCombinations *= $numValues;
	}
	
	/** ПОЛУЧИТЬ СЛЕДУЮЩУЮ КОМБИНАЦИЮ МНОЖИТЕЛЕЙ */
	public function getNextCombination(){
		
		if ($this->curCombination == $this->numCombinations)
			return null;
		
		$values = array();
		$increaseLevel = $this->curCombination > 0;
		
		foreach($this->multipliers as $file => &$multiplier){
			$values[$file] = array();
			
			foreach($multiplier as &$data){
				
				if ($increaseLevel) { // увеличение текущего множителя
					
					if ($data['curIndex'] == $data['maxIndex']) {
						$data['curIndex'] = 0;
						$increaseLevel = TRUE;
					} else {
						$data['curIndex']++;
						$increaseLevel = FALSE;
					}
				}
				
				$values[$file][] = array(
					'row' => $data['row'],
					'value' => $data['valuesArr'][ $data['curIndex'] ],
					'valuesStr' => $data['valuesStr'],
				);
			}
		}
		
		$this->curCombination++;
		return $values;
	}
}

?>