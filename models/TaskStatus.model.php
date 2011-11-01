<?

class TaskStatus {
	
	public $statuses = array();
	public $nameIdIndex = array();
	
	private static $_instance = null;
	
	
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new TaskStatus();
		
		return self::$_instance;
	}
	
	private function __construct(){
		
		$this->statuses = db::get()->getAllIndexed('SELECT * FROM task_states', 'id');
		foreach($this->statuses as $s){
			$this->nameIdIndex[$s['name']] = $s['id'];
		}
	}

}

?>