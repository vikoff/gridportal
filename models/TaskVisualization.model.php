<?php

class TaskVisualization {
	
	const TYPE_TABLE = 'table';
	const TYPE_CSV_CHART = 'chart_csv';
	const TYPE_IMAGE = 'image';
	
	public $fullname = null;
	public $type = null;
	
	public function __construct($fullname){
		
		$this->fullname = $fullname;
		$this->type = $this->_getVisualType();
	}
	
	protected function _getVisualType(){
		
		$ext = Tools::getExt($this->fullname);
		if ($ext == 'csv')
			return self::TYPE_CSV_CHART;
		elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png')
			return self::TYPE_IMAGE;
		else
			return self::TYPE_TABLE;
	}
	
	protected function _buildTable(){
		
		$file = file($this->fullname);
		$rows = array();
		foreach ($file as $row) {
			$pair = explode('=', trim($row), 2);
			if (count($pair) == 2)
				$rows[] = $pair;
		}
		$data = array('rows' => $rows);
		return FrontendViewer::get()->getContentPhpFile(TaskSubmitController::TPL_PATH.'visualization/'.$this->type.'.php', $data);
	}
	
	protected function _buildCsvChart(){
		
		// $file = file($this->fullname);
		$f = fopen($this->fullname, 'r');
		$rows = array();
		while($row = fgetcsv($f))
			$rows[] = $row;
		//echo '<pre>'; print_r($rows); die;
		/*$rows = array();
		foreach ($file as $row) {
			$pair = explode('=', trim($row), 2);
			if (count($pair) == 2)
				$rows[] = $pair;
		}*/
		$data = array('rows' => $rows);
		return FrontendViewer::get()->getContentPhpFile(TaskSubmitController::TPL_PATH.'visualization/csv_chart.php', $data);
	}
	//@TODO: ДОДЕЛАТЬ ЗАВТРА!!!
	protected function _buildImage(){
		
		$data = array(
			'fullname' => $this->fullname,
			'filename' => WWW_ROOT.substr($this->fullname, strlen(FS_ROOT))
		);
		return FrontendViewer::get()->getContentPhpFile(TaskSubmitController::TPL_PATH.'visualization/image.php', $data);
	}
	
	public function getHtml(){
		
		switch ($this->type) {
			case self::TYPE_TABLE:
				return $this->_buildTable();
			case self::TYPE_IMAGE:
				return $this->_buildImage();
			case self::TYPE_CSV_CHART:
				return $this->_buildCsvChart();
			default:
				return 'no type detected';
		}
		
	}
	
}

?>