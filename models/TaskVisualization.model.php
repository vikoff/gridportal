<?php

class TaskVisualization {
	
	const TYPE_RAW = 'raw';
	const TYPE_TABLE = 'table';
	const TYPE_CSV_CHART = 'chart_csv';
	const TYPE_IMAGE = 'image';
	const TYPE_VIDEO = 'video';
	const TYPE_PDF = 'pdf';
	const TYPE_ARCHIVE = 'archive';
	
	public $fullname = null;
	public $type = null;
	
	public function __construct($fullname){
		
		$this->fullname = $fullname;
		$this->type = self::getVisualType($this->fullname);
	}
	
	public static function getVisualType($fullname){
		
		$ext = Tools::getExt($fullname);
		if ($ext == 'csv')
			return self::TYPE_CSV_CHART;
		elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png')
			return self::TYPE_IMAGE;
		elseif ($ext == 'avi' || $ext == 'mpeg' || $ext == 'mp4')
			return self::TYPE_VIDEO;
		elseif ($ext == 'pdf')
			return self::TYPE_PDF;
		elseif (in_array($ext, array('rar', 'zip', 'tar', 'gz', 'bz')))
			return self::TYPE_ARCHIVE;
		else
			return self::TYPE_RAW;
	}
	
	public function getHtml(){
		
		switch ($this->type) {
			case self::TYPE_RAW:
				return $this->_buildRaw();
			case self::TYPE_TABLE:
				return $this->_buildTable();
			case self::TYPE_IMAGE:
				return $this->_buildImage();
			case self::TYPE_CSV_CHART:
				return $this->_buildCsvChart();
			case self::TYPE_VIDEO:
				return $this->_buildVideo();
			default:
				return 'no type detected';
		}
		
	}
	
	protected function _buildRaw(){
		
		return '<pre>'.file_get_contents($this->fullname).'</pre>';
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
	
	protected function _buildVideo(){
		
		$data = array(
			'fullname' => $this->fullname,
			'filename' => WWW_ROOT.substr($this->fullname, strlen(FS_ROOT))
		);
		return FrontendViewer::get()->getContentPhpFile(TaskSubmitController::TPL_PATH.'visualization/video.php', $data);
	}
	
}

?>