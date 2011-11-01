<?

/**
 * Класс для работы с сообщениями для пользователей.
 * @using nothing
 */
class Messenger{
	
	// имя $_GET параметра для передачи идентификатора
	private $_qsParamName = 'fmsgid';
	
	// контейнеры пользовательских сообщений
	private $_successMsgs = array();
	private $_infoMsgs = array();
	private $_errorMsgs = array();
	
	// ключ для будущих сообщений
	private $_futureKey = null;
	
	// полученный идентификатор будущих сообщений
	private $_receivedFutureKey = null;
	
	private static $_instance = null;
	
	// ТОЧКА ВХОДА В КЛАСС (ПОЛУЧИТЬ ЭКЗЕМПЛЯР Messenger)
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new Messenger();
		
		return self::$_instance;
	}
	
	// КОНСТРУКТОР
	private function __construct(){
		
		$this->_receivedFutureKey = !empty($_REQUEST[$this->_qsParamName]) ? (int)$_REQUEST[$this->_qsParamName] : 0;
		$this->future2present();
	}
	
	public function addSuccess($msg){
	
		$this->_successMsgs[] = str_replace(array("\n", "\r\n"), array('<br />', '<br />'), $msg);
	}
	
	public function addInfo($msg){
	
		$this->_infoMsgs[] = str_replace(array("\n", "\r\n"), array('<br />', '<br />'), $msg);
	}
	
	public function addError($msg){
	
		$this->_errorMsgs[] = str_replace(array("\n", "\r\n"), array('<br />', '<br />'), $msg);
	}
	
	// ПРОВЕРКА ИНИЦИАЛИЗАЦИИ МЕХАНИЗМА БУДУЩИХ СООБЩЕНИЙ
	private function _checkFutureInitialization(){
	
		if(!isset($_SESSION['featureUserMessages']))
			$_SESSION['featureUserMessages'] = array('lastKey' => 0, 'messages' => array());
	}
	
	// ПОЛУЧИТЬ ТЕКУЩИЙ КЛЮЧ ДЛЯ БУДУЩИХ СООБЩЕНИЙ
	public function getFutureKey(){
		
		$this->_checkFutureInitialization();
		
		if(is_null($this->_futureKey)){
			$this->_futureKey = ++$_SESSION['featureUserMessages']['lastKey'];
		}
		return $this->_futureKey;
	}
	
	// ДОБАВИТЬ БУДУЩЕЕ СООБЩЕНИЕ ОБ УСПЕШНОМ ВЫПОЛНЕНИИ
	public function addFutureSuccess($msg){
		
		$identifier = $this->getFutureKey();
		$_SESSION['featureUserMessages']['messages'][$identifier]['success'][] = str_replace(array("\n", "\r\n"), '<br />', $msg);
	}
	
	// ДОБАВИТЬ БУДУЩЕЕ ИНФОРМАЦИОННОЕ СООБЩЕНИЕ
	public function addFutureInfo($msg){
	
		$identifier = $this->getFutureKey();
		$_SESSION['featureUserMessages']['messages'][$identifier]['info'][] = str_replace(array("\n", "\r\n"), '<br />', $msg);
	}
	
	// ДОБАВИТЬ БУДУЩЕЕ СООБЩЕНИЕ ОБ ОШИБКЕ
	public function addFutureError($msg){
	
		$identifier = $this->getFutureKey();
		$_SESSION['featureUserMessages']['messages'][$identifier]['error'][] = str_replace(array("\n", "\r\n"), '<br />', $msg);
	}
	
	// ПРОВЕРИТЬ, ИМЕЮТСЯ ЛИ БУДУЩИЕ СООБЩЕНИЯ
	public function hasFuture(){
	
		$identifier = $this->getFutureKey();
		return isset($_SESSION['featureUserMessages']['messages'][$identifier]);
	}
	
	// ПОЛУЧИТЬ ВСЕ БУДУЩИЕ СООБЩЕНИЯ
	public function getFuture(){
		
		$identifier = $this->_receivedFutureKey;
		$output = array('success' => array(), 'info' => array(), 'error' => array());
		
		if(!$identifier || empty($_SESSION['featureUserMessages']['messages'][$identifier]))
			return $output;
		
		if(!empty($_SESSION['featureUserMessages']['messages'][$identifier]['success']))
			$output['success'] = $_SESSION['featureUserMessages']['messages'][$identifier]['success'];
		
		if(!empty($_SESSION['featureUserMessages']['messages'][$identifier]['info']))
			$output['info'] = $_SESSION['featureUserMessages']['messages'][$identifier]['info'];
		
		if(!empty($_SESSION['featureUserMessages']['messages'][$identifier]['error']))
			$output['error'] = $_SESSION['featureUserMessages']['messages'][$identifier]['error'];
		
		unset($_SESSION['featureUserMessages']['messages'][$identifier]);
		
		return $output;
	}
	
	// ПОЛУЧИТЬ ВСЕ ПОЛЬЗОВАТЕЛЬСКИЕ СООБЩЕНИЯ
	public function getAll(){
		
		$html = '';
		
		if(count($this->_successMsgs))
			$html .= '<div class="userMessageSuccess">'.implode('<br />', $this->_successMsgs).'</div>';
		
		if(count($this->_infoMsgs))
			$html .= '<div class="userMessageInfo">'.implode('<br />', $this->_infoMsgs).'</div>';
		
		if(count($this->_errorMsgs))
			$html .= '<div class="userMessageError">'.implode('<br />', $this->_errorMsgs).'</div>';
		
		$this->clearPresent();
		
		return $html;
	}
	
	// ДОБАВИТЬ КЛЮЧ БУДУЩИХ СООБЩЕНИЙ В QS
	public function qsAppendFutureKey($qs){
		
		$this->present2future();
		
		// если ключ будущих сообщений уже присутствует в QS
		if(strpos($qs, $this->_qsParamName.'=')){
			$key = $this->hasFuture() ? $this->getFutureKey() : 0;
			$qs = preg_replace('/'.$this->_qsParamName.'=\d*/', $this->_qsParamName.'='.$key, $qs);
		}
		// если ключ не присутствует, а добавить надо
		elseif($this->hasFuture()){
			$concatenator = '';
			if(strpos($qs, '?') === FALSE){
				$concatenator = '?';
			}else{
				$lastChar = substr($qs, -1, 1);
				$concatenator = ($lastChar == '?' || $lastChar == '&') ? '' : '&';
			}
			$qs .= $concatenator.$this->_qsParamName.'='.$this->getFutureKey();
		}
			
		return $qs;
	}
	
	public function clearPresent(){
	
		$this->_successMsgs = array();
		$this->_infoMsgs = array();
		$this->_errorMsgs = array();
	}
	
	// ПРЕОБРАЗОВАТЬ НАСТОЯЩИЕ СООБЩЕНИЯ В БУДУЩИЕ
	public function present2future(){
		
		foreach($this->_successMsgs as $msg)
			$this->addFutureSuccess($msg);
		
		foreach($this->_infoMsgs as $msg)
			$this->addFutureInfo($msg);
		
		foreach($this->_errorMsgs as $msg)
			$this->addFutureError($msg);
		
		$this->clearPresent();
	}
	
	// ПРЕОБРАЗОВАТЬ БУДУЩИЕ СООБЩЕНИЯ В НАСТОЯЩИЕ
	public function future2present(){
		
		$futureArr = $this->getFuture();
		
		foreach($futureArr['success'] as $msg)
			$this->addSuccess($msg);
		
		foreach($futureArr['info'] as $msg)
			$this->addInfo($msg);
		
		foreach($futureArr['error'] as $msg)
			$this->addError($msg);
	}
	
}

?>