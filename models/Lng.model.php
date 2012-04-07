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
	
	/** последний сохраненный id */
	private $_lastId = 0;
	
	/** ЗАДАТЬ ТЕКУЩИЙ ЯЗЫК ДО ИНИЦИАЛИЗАЦИИ КЛАССА */
	public static function setCurLanguage($lng){
		
		if ($lng && in_array($lng, self::$allowedLngs))
			self::$_assignedCurLng = $lng;
	}
	
	public static function getLngTitle($lng){
		
		return self::$lngTitles[$lng];
	}
	
	/**
	 * СОХРАНЕНИЕ ЯЗЫКОВОГО ФРАГМЕНТА
	 * @param array $data - массив с ключами
	 *                      'name', 'num_placeholders', 'description', 'is_external', 'text'=>array('ru', 'en', 'ua')
	 * @return string 'ok' если сохранено успешно, или текст сообщения об ошибке
	 */
	public function save($data){
		
		$this->_lastId = 0;
		
		$id = getVar($data['id'], 0, 'int');
		$name = trim(getVar($data['name']));
		$db = db::get();
		
		if(empty($name))
			return 'Ключ не должно быть пустым';
		
		$fields = array(
			'name' => $name,
			'num_placeholders' => getVar($data['num_placeholders'], 0, 'int'),
			'description' => getVar($data['description']),
			'is_external' => !empty($data['is_external']),
		);
		
		if($id){
			if(!$db->getOne('SELECT COUNT(1) FROM lng_snippets WHERE id='.$id, 0))
				return 'Запись не найдена';
			if($db->getOne('SELECT COUNT(1) FROM lng_snippets WHERE name='.$db->qe($name).' AND id!='.$id , 0))
				return 'Фрагмент с таким ключом уже существует';
			
			$db->update('lng_snippets', $fields, 'id='.$id);
			foreach(self::$allowedLngs as $l)
				$db->update('lng_'.$l, array('text' => !empty($data['text'][$l]) ? $data['text'][$l] : null), 'snippet_id='.$id);
			$this->_lastId = $id;
		}
		else{
			if($db->getOne('SELECT COUNT(1) FROM lng_snippets WHERE name='.$db->qe($name), 0))
				return 'Фрагмент с таким ключом уже существует';
			$id = $db->insert('lng_snippets', $fields);
			foreach(self::$allowedLngs as $l)
				$db->insert('lng_'.$l, array('snippet_id' => $id, 'text' => !empty($data['text'][$l]) ? $data['text'][$l] : null));
			$this->_lastId = $id;
		}
		
		$this->snippets[$name] = !empty($data['text'][$this->_curLng])
			? $data['text'][$this->_curLng]
			: (!empty($data['text'][self::$defaultLng])
				? $data['text'][self::$defaultLng]
				: $name);
		
		return 'ok';
	}
	
	public function update($idOrName, $data){
		
		$db = db::get();
		$name = $data['name'];
		
		$where = is_numeric($idOrName)
			? 'id='.$idOrName
			: 'name='.$db->qe($idOrName);
		
		$fields = array(
			'num_placeholders' => getVar($data['num_placeholders'], 0, 'int'),
			'description' => getVar($data['description']),
			'is_external' => !empty($data['is_external']),
		);
		
		if(! ($id = $db->getOne('SELECT id FROM lng_snippets WHERE '.$where, 0)))
			return 'Запись не найдена';
		
		$db->update('lng_snippets', $fields, 'id='.$id);
		foreach(self::$allowedLngs as $l)
			$db->update('lng_'.$l, array('text' => !empty($data['text'][$l]) ? $data['text'][$l] : null), 'snippet_id='.$id);
		$this->_lastId = $id;
		
		$this->snippets[$name] = !empty($data['text'][$this->_curLng])
			? $data['text'][$this->_curLng]
			: (!empty($data['text'][self::$defaultLng])
				? $data['text'][self::$defaultLng]
				: $name);
		
		return 'ok';
	}
	
	public function getLastId(){
		
		return $this->_lastId;
	}
	
	/** УДАЛЕНИЕ ЯЗЫКОВОГО ФРАГМЕНТА */
	public function delete($idOrName){
		
		$db = db::get();
		$where = is_numeric($idOrName)
			? 'id='.$idOrName
			: 'name='.$db->qe($idOrName);
			
		if( ! ($id = $db->getOne('SELECT id FROM lng_snippets WHERE '.$where, 0)))
			return 'Запись не найдена';
			
		$db->delete('lng_snippets', 'id='.$id);
		foreach(self::$allowedLngs as $l)
			$db->delete('lng_'.$l, 'snippet_id='.$id);
			
		return 'ok';
	}
	
	/**
	 * ПОЛУЧИТЬ ФРАГМЕНТ ТЕКСТА ПО ЗАДАННОМУ КЛЮЧУ ИЛИ ЭКЗЕМПЛЯР КЛАССА LNG
	 * @param null|string $key - ключ вида "sect1.sect2.part3"
	 * @param array $placeholders - массив подстановщиков (строки, которые будут подставлены
	 *                              вместо $1, $2, $n в результирующую строку. Нумерация начинается с 1)
	 * @return Lng-instance|string - экземпляр класса lng или языковой фрагмент
	 */
	public static function get($key = null, $placeholders = array()){
		
		if(is_null(self::$_instance))
			self::$_instance = new Lng();
		
		if(is_null($key))
			return self::$_instance;
		
		return self::$_instance->getSnippet($key, $placeholders);
		
	}
	
	/**
	 * ПОЛУЧИТЬ ФРАГМЕНТ ТЕКСТА ПО ЗАДАННОМУ КЛЮЧУ С УЧЕТОМ СКЛОНЕНИЯ ИЛИ ЭКЗЕМПЛЯР КЛАССА LNG
	 * в случае если число - 1, ключ не меняется,
	 * если число заканчивается на 1* (напр. 21) - в конце ключа добавляется 1,
	 * если число заканчивается на 2, 3 или 4 - в конце добавляется 2,
	 * если число заканчивается на 5-9 или 0 - в конце добавляется 3
	 * * - исключение числа с 11 по 14, в этом случае в конце добавляется 3
	 * 
	 * исп.: <?= Lng::getDeclinated('task-set.will-run-tasks', $this->numSubmits, array($this->numSubmits)); ?>
	 * 								 ключ					   число					число (еще раз)	
	 */
	public static function getDeclinated($key, $number, $placeholders = array()){
		
		if(is_null(self::$_instance))
			self::$_instance = new Lng();
		
		if ($number != 1){
			$cases = array(2, 0, 1, 1, 1, 2);
			$variant = (($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[($number % 10 < 5) ? $number % 10 : 5]) + 1;
		}
		else
			$variant = '';
		
		return self::$_instance->getSnippet($key.$variant, $placeholders);
		
	}
	
	/** КОНСТРУКТОР */
	private function __construct(){
		
		$this->_check();
		$this->_loadSnippets();
		
		if (defined('USER_AUTH_ID') && USER_AUTH_ID)
			$this->_saveUsrLng();
	}
	
	/**
	 * ПОЛУЧИТЬ ФРАГМЕНТ ТЕКСТА ПО ЗАДАННОМУ КЛЮЧУ
	 * @param tring $key - ключ вида "sect1.sect2.part3"
	 * @return string - языковой фрагмент
	 */
	public function getSnippet($key, $placeholders = array()){
		
		if(!isset($this->snippets[$key]))
			$this->save(array('id' => 0, 'name' => $key, 'num_placeholders' => count($placeholders)));
		
		$text = $this->snippets[$key];
		
		if (!empty($placeholders)) {
			foreach (array_values($placeholders) as $index => $val)
				$text = str_replace('$'.($index + 1), $val, $text);
		}
		return $text;
	}
	
	/** извлечение языкового фрагмента указанного языка (осторжно, не кэшируемый SQL) */
	public function getLngSnippet($lng, $key, $placeholders = array()){
		
		$db = db::get();
		
		$text = $db->getOne('
			SELECT COALESCE(lng.text, lng_default.text, s.name) AS text FROM lng_snippets s
			LEFT JOIN lng_'.$lng.' lng ON lng.snippet_id = s.id
			LEFT JOIN lng_'.self::$defaultLng.' lng_default ON lng_default.snippet_id = s.id
			WHERE s.name='.$db->qe($key).'
		');		
		
		if (!empty($placeholders)) {
			foreach (array_values($placeholders) as $index => $val)
				$text = str_replace('$'.($index + 1), $val, $text);
		}
		return $text;
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
	
	private function _saveUsrLng(){
		
		if (empty($_SESSION['usr-lng']) || $_SESSION['usr-lng'] != $this->_curLng) {
			
			$curUser = CurUser::get();
			$curUser->setField('lng', $this->_curLng);
			$curUser->_save();
			$_SESSION['usr-lng'] = $this->_curLng;
		}
	}
	public static function getAll(){
		
		$fields = '';
		$joins = '';
		foreach(self::$allowedLngs as $k => $lng){
			$fields .= ', '.$lng.'.text AS '.$lng;
			$joins  .= ' LEFT JOIN lng_'.$lng.' '.$lng.' ON '.$lng.'.snippet_id = s.id ';
		}
		return db::get()->getAll('SELECT s.* '.$fields.' FROM lng_snippets s '.$joins.' WHERE is_external=0 ORDER BY s.name');
	}
	
	public static function getSnippetAllData($idOrName){
		
		$db = db::get();
		$where = is_numeric($idOrName)
			? 's.id='.$idOrName
			: 's.name='.$db->qe($idOrName);
	
		$fields = '';
		$joins = '';
		foreach(self::$allowedLngs as $k => $lng){
			$fields .= ', '.$lng.'.text AS '.$lng;
			$joins  .= ' LEFT JOIN lng_'.$lng.' '.$lng.' ON '.$lng.'.snippet_id = s.id ';
		}
		
		return $db->getRow('SELECT s.* '.$fields.' FROM lng_snippets s '.$joins.' WHERE '.$where, FALSE);
	}
	
	public static function getAllLngs($idOrName){
		
		$db = db::get();
		$where = is_numeric($idOrName)
			? 's.id='.$idOrName
			: 's.name='.$db->qe($idOrName);
	
		$fields = array();
		$joins = '';
		foreach(self::$allowedLngs as $k => $lng){
			$fields[] = $lng.'.text AS '.$lng;
			$joins  .= ' LEFT JOIN lng_'.$lng.' '.$lng.' ON '.$lng.'.snippet_id = s.id ';
		}
		
		return $db->getRow('SELECT '.implode(',', $fields).' FROM lng_snippets s '.$joins.' WHERE '.$where, FALSE);
	}
	
}

?>