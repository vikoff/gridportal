<?

class Storage{
	
	private static $_instance = null;
	
	private $_filePath = null;
	
	public $data = array();
	
	
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new Storage();
		
		return self::$_instance;
	}
	
	private function __construct(){
		
		$this->_filePath = dirname(__FILE__).'/data.txt';
		
		$this->data = file_get_contents($this->_filePath);
		$this->data = strlen($this->data)
			? unserialize($this->data)
			: array();
	}
	
	public function save(){
		
		file_put_contents($this->_filePath, serialize($this->data));
	}
	
}