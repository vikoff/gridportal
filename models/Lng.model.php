<?

class Lng {
	
	/** список допустимых языков */
	public static $allowedLngs = array('ru', 'ua', 'en');
	
	public static $lngTitles = array(
		'ru' => 'Русский',
		'ua' => 'Украинский',
		'en' => 'Английский',
	);
	
	/** язык по умолчанию (если не удастся определить язык пользователя) */
	public static $defaultLng = 'ru';
	
	/** экземпляр класса Lng */
	private static $_instance = null;
	
	/** назначенный текущий язык */
	private static $_assignedCurLng = null;
	
	/** текущий язык */
	private $_curLng = null;
	
	/** языковые фрагменты */
	public $snippets = null;
	
	/** все языковые данные */
	private $_allData = null;
	
	
	/** ЗАДАТЬ ТЕКУЩИЙ ЯЗЫК ДО ИНИЦИАЛИЗАЦИИ КЛАССА */
	public static function setCurLanguage($lng){
		
		self::$_assignedCurLng = $lng;
	}
	
	public static function getLngTitle($lng){
		
		return self::$lngTitles[$lng];
	}
	
	/** СОХРАНЕНИЕ ЯЗЫКОВОГО ФРАГМЕНТА */
	public function save($data){
		
		$id = getVar($data['id'], 0, 'int');
		$name = trim(getVar($data['name']));
		$db = db::get();
		
		if(empty($name))
			return 'Ключ не должно быть пустым';
		
		if($id){
			if(!$db->getOne('SELECT COUNT(1) FROM lng_snippets WHERE id='.$id, 0))
				return 'Запись не найдена';
			if($db->getOne('SELECT COUNT(1) FROM lng_snippets WHERE name='.$db->qe($name).' AND id!='.$id , 0))
				return 'Фрагмент с таким ключом уже существует';
			
			$db->update('lng_snippets', array('name' => $name, 'description' => getVar($data['description'])), 'id='.$id);
			foreach(self::$allowedLngs as $l)
				$db->update('lng_'.$l, array('text' => !empty($data['text'][$l]) ? $data['text'][$l] : null), 'snippet_id='.$id);
		}
		else{
			if($db->getOne('SELECT COUNT(1) FROM lng_snippets WHERE name='.$db->qe($name), 0))
				return 'Фрагмент с таким ключом уже существует';
			$id = $db->insert('lng_snippets', array('name' => $name, 'description' => getVar($data['description'])));
			foreach(self::$allowedLngs as $l)
				$db->insert('lng_'.$l, array('snippet_id' => $id, 'text' => !empty($data['text'][$l]) ? $data['text'][$l] : null));
		}
		
		$this->snippets[$name] = !empty($data['text'][$this->_curLng])
			? $data['text'][$this->_curLng]
			: (!empty($data['text'][self::$defaultLng])
				? $data['text'][self::$defaultLng]
				: $name);
		
		return 'ok';
	}
	
	/** УДАЛЕНИЕ ЯЗЫКОВОГО ФРАГМЕНТА */
	public function delete($id){
		
		$db = db::get();
		if(!$db->getOne('SELECT COUNT(1) FROM lng_snippets WHERE id='.$id, 0))
			return 'Запись не найдена';
			
		$db->delete('lng_snippets', 'id='.$id);
		foreach(self::$allowedLngs as $l)
			$db->delete('lng_'.$l, 'snippet_id='.$id);
			
		return 'ok';
	}
	
	/**
	 * ПОЛУЧИТЬ ФРАГМЕНТ ТЕКСТА ПО ЗАДАННОМУ КЛЮЧУ ИЛИ ЭКЗЕМПЛЯР КЛАССА LNG
	 * @param null|string $key - ключ вида "sect1.sect2.part3"
	 * @return Lng-instance|string - экземпляр класса lng или языковой фрагмент
	 */
	public static function get($key = null){
		
		if(is_null(self::$_instance))
			self::$_instance = new Lng();
		
		if(is_null($key))
			return self::$_instance;
		
		return self::$_instance->getSnippet($key);
	}
	
	/** КОНСТРУКТОР */
	private function __construct(){
		
		$this->_check();
		$this->_loadSnippets();
	}
	
	/**
	 * ПОЛУЧИТЬ ФРАГМЕНТ ТЕКСТА ПО ЗАДАННОМУ КЛЮЧУ
	 * @param tring $key - ключ вида "sect1.sect2.part3"
	 * @return string - языковой фрагмент
	 */
	public function getSnippet($key){
		
		if(!isset($this->snippets[$key]))
			$this->save(array('id' => 0, 'name' => $key));
		
		return $this->snippets[$key];
	}
	
	/**
	 * ПОЛУЧИТЬ ТЕКУЩИЙ ЯЗЫК
	 * @return string - текущий язык
	 */
	public function getCurLng(){
		return $this->_curLng;
	}
	
	/** ПОЛУЧИТЬ МАССИВ ЯЗЫКОВ ИЗ ACCEPT_LANGUAGE */
	public function getAcceptLngs(){

		if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			return array();
		
		$lngs = array();
		
		foreach(explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $value) {
			if(strpos($value, ';') !== false)
				list($value) = explode(";", $value);
			if(strpos($value, '-') !== false)
				list($value) = explode("-", $value);
			if(in_array($value, array('uk', 'ukr')))
				$value = 'ua';
			if(in_array($value, array('us')))
				$value = 'en';
			
			$value = strtolower($value);
			if(in_array($value, self::$allowedLngs))
				$lngs[] = $value;
		}
		
		return $lngs;
	}
	
	/** ПРОВЕРКА ТЕКУЩЕГО ЯЗЫКА */
	private function _check(){
		
		// если язык задан до инициализации класса
		if(!is_null(self::$_assignedCurLng)){
			$this->_curLng = self::$_assignedCurLng;
			return;
		}
		
		// если язык передан через GET
		if(!empty($_GET['lng']) && in_array($_GET['lng'], self::$allowedLngs)){
			$this->_curLng = $_GET['lng'];
			return;
		}
		
		// в случае, если язык не определился вышеуказанными способами
		// возьмем его из ACCEPT_LANGUAGE или же язык по умолчанию
		$accepts = $this->getAcceptLngs();
		$this->_curLng = count($accepts) ? $accepts[0] : self::$defaultLng;
	}
	
	/** ЗАГРУЗКА ЯЗЫКОВЫХ ФРАГМЕНТОВ */
	private function _loadSnippets(){
		
		$this->snippets = db::get()->getColIndexed('
			SELECT s.name, COALESCE(lng.text, lng_default.text, s.name) AS text FROM lng_snippets s
			LEFT JOIN lng_'.$this->_curLng.' lng ON lng.snippet_id = s.id
			LEFT JOIN lng_'.self::$defaultLng.' lng_default ON lng_default.snippet_id = s.id
		');
		
		// echo'<pre>'; print_r($this->snippets); die;
	}
	
	public static function getAll(){
		
		$fields = '';
		$joins = '';
		foreach(self::$allowedLngs as $k => $lng){
			$fields .= ', '.$lng.'.text AS '.$lng;
			$joins  .= ' LEFT JOIN lng_'.$lng.' '.$lng.' ON '.$lng.'.snippet_id = s.id ';
		}
		return db::get()->getAll('SELECT s.id, s.name, s.description '.$fields.' FROM lng_snippets s '.$joins.' ORDER BY s.name');
	}
	
	public static function getSnippetAllData($id){
	
		$fields = '';
		$joins = '';
		foreach(self::$allowedLngs as $k => $lng){
			$fields .= ', '.$lng.'.text AS '.$lng;
			$joins  .= ' LEFT JOIN lng_'.$lng.' '.$lng.' ON '.$lng.'.snippet_id = s.id ';
		}
		
		return db::get()->getRow('SELECT s.id, s.name, s.description '.$fields.' FROM lng_snippets s '.$joins.' WHERE s.id='.(int)$id, FALSE);
	}
}

?>