<?

class Page extends GenericObject{
	
	const TABLE = 'pages';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';
	
	const TYPE_FULL = 1;
	const TYPE_CHUNK = 2;

	/** данные для сохранения в оригинальном виде */
	private $_originData = array();
	
	/** валидные языковые фрагменты */
	private $validLngParts = array();
	
	public static $lngFields = array('title', 'body', 'meta_description', 'meta_keywords');
	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new Page(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new Page($id, self::INIT_EXISTS);
	}

	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new Page($id, self::INIT_EXISTS_FORCE, $fieldvalues);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА ПО ПСЕВДОНИМУ) */
	public static function loadByAlias($alias){
		
		$db = db::get();
		$lng = Lng::get();
		
		$fields = array();
		foreach(self::$lngFields as $f)
			$fields[] = 'COALESCE(lng.'.$f.', lng_default.'.$f.', \'\') AS '.$f;
		
		$sql = '
			SELECT p.*, '.implode(', ', $fields).' FROM '.self::TABLE.' p
			LEFT JOIN pages_'.$lng->getCurLng().' lng ON lng.page_id = p.id
			LEFT JOIN pages_'.Lng::$defaultLng.' lng_default ON lng_default.page_id = p.id
			WHERE alias='.$db->qe($alias).'
			LIMIT 1';
		
		$data = $db->getRow($sql);
		if(!$data)
			throw new Exception404(self::NOT_FOUND_MESSAGE);
			
		return new Page($data['id'], self::INIT_EXISTS_FORCE, $data);
	}
	
	/** СЛУЖЕБНЫЙ МЕТОД (получение констант из родителя) */
	public function getConst($name){
		return constant(__CLASS__.'::'.$name);
	}
	
	/**
	 * ПРОВЕРКА ВОЗМОЖНОСТИ ДОСТУПА К ОБЪЕКТУ
	 * Вызывается автоматически при загрузке существующего объекта
	 * В случае запрета доступа генерирует нужное исключение
	 */
	protected function _accessCheck(){
		
		if(!App::$adminMode && !$this->getField('published'))
			throw new Exception403('Доступ к странице ограничен');
	}
	
	/**
	 * ДОЗАГРУЗКА ДАННЫХ
	 * выполняется после основной загрузки данных из БД
	 * и только для существующих объектов
	 * @param array &$data - данные полученные основным запросом
	 * @return void
	 */
	protected function afterLoad(&$data){
		
		if(App::$adminMode)
			$this->_loadLngs();
	}
	
	/** ЗАГРУЗКА ВСЕХ ЯЗЫКОВ */
	private function _loadLngs(){
		
		$db = db::get();
		$data = array();
		
		// загрузка языковых частей
		foreach(Lng::$allowedLngs as $l)
			$data[$l] = $db->getRow('SELECT '.implode(',', self::$lngFields).' FROM pages_'.$l.' WHERE page_id='.$this->id);
		
		// формирование массива вида $data['field']['lng'] = 'val'
		foreach(self::$lngFields as $f){
			$this->dbFieldValues[$f] = array();
			foreach(Lng::$allowedLngs as $l){
				$this->dbFieldValues[$f][$l] = !empty($data[$l][$f]) ? $data[$l][$f] : '';
			}
		}
		
		// echo '<pre>'; print_r($this->dbFieldValues); die;
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		if(is_null($this->validator)){
		
			$this->validator = new Validator();
			$this->validator->rules(array(), array(
                'alias' => array('trim' => TRUE, 'match' => '/^[\w\-]{0,255}$/'),
				'type' => array('settype' => 'int', 'in' => array(self::TYPE_FULL, self::TYPE_CHUNK)),
                'published' => array('checkbox' => array('on' => '1', 'off' => '0')),
                'locked' => array('checkbox' => array('on' => '1', 'off' => '0')),
            ));
			$this->validator->setFieldTitles(array(
                'id' => 'id',
                'alias' => 'Псевдоним',
                'published' => 'Опубликовать',
            ));
		}
		
		// применение специальных правил для редактирования или добавления объекта
		if($this->isExistsObj){
		
		}
		
		return $this->validator;
	}
	
	public function preValidation(&$data){
		
		$this->_originData = $data;
	}
	
	// ПОДГОТОВКА ДАННЫХ К СОХРАНЕНИЮ
	public function postValidation(&$data){
		
		if(!$this->_checkAlias($data))
			return FALSE;
		
		if(!$this->_validateLngsParts($this->_originData))
			return FALSE;
			
		$data['modif_date'] = time();
		
		if($this->isNewObj){
			$data['author'] = USER_AUTH_ID;
			$data['create_date'] = time();
		}
	}
	
	/** ПРОВЕРКА ПСЕВДОНИМА */
	private function _checkAlias($data){
	
		// проверка псевдонима на уникальность (если задан)
		if(strlen($data['alias']) && db::get()->getOne('SELECT COUNT(1) FROM '.self::TABLE.' WHERE alias='.db::get()->qe($data['alias']).' '.($this->isExistsObj ? ' AND id!='.$this->id : ''), 0)){
			$this->setError('Запись с таким псевдонимом уже существует');
			return FALSE;
		}
		
		// проверка чтобы псевдоним не был числом, соответствующим id другой страницы
		if($this->isNewObj && is_numeric($data['alias'])){
			$this->setError('Псевдоним новой записи не может быть задан числом.');
			return FALSE;
		}
		
		// проверка если id задан числом, он должен совпадать с id текущей записи
		if($this->isExistsObj && is_numeric($data['alias']) && $data['alias'] != $this->id){
			$this->setError('Если псевдоним задан числом, то он должен совпадать с id записи.');
			return FALSE;
		}
		
		return TRUE;
	}
	
	/** ПРОВЕРКА ЯЗЫКОВЫХ ФРАГМЕНТОВ */
	private function _validateLngsParts(&$data){
		
		$curLng = Lng::get()->getCurLng();
		$lngData = array();
		
		$validator = new Validator(array(), array(
			'title' => array('strip' => TRUE, 'length' => array('max' => '65535')),
			'body' => array('length' => array('max' => '65535')),
			'meta_description' => array('strip' => TRUE, 'length' => array('max' => '65535')),
			'meta_keywords' => array('strip' => TRUE, 'length' => array('max' => '65535')),
		));
		$validator->setFieldTitles(array(
			'title'            => 'Заголовок',
			'body'             => 'Тело страницы',
			'meta_description' => 'meta description',
			'meta_keywords'    => 'meta keywords',
		));
		
		// группировка данных по языкам и валидация
		foreach(Lng::$allowedLngs as $l){
			
			$this->validLngParts[$l] = array();
			
			foreach(self::$lngFields as $f)
				$this->validLngParts[$l][$f] = !empty($data[$f][$l]) ? $data[$f][$l] : '';
			
			// дополнительные правила валидации для текущего языка
			$additRules = $l == $curLng ? array('title' => array('required' => TRUE)) : null;
			
			// валидация
			$this->validLngParts[$l] = $validator->validate($this->validLngParts[$l], $additRules);
			if($validator->hasError()){
				$this->setError(
					'<div style="padding: 5px; margin: 5px; border :solid 1px black;">'
						.'<div style="font-weight: bold">'.Lng::getLngTitle($l).'</div>'
						.$validator->getError()
					.'</div>'
				);
			}else{
				// $this->validLngParts[$l]['body'] = str_replace(array("\r\n", "\n"), '', $this->validLngParts[$l]['body']);
				foreach($this->validLngParts[$l] as &$v)
					$v = !empty($v) ? $v : null;
			}
		}
		
		return !$this->hasError();
	}
	
	// ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ
	public function afterSave($data){
	
		if(!strlen($data['alias'])){
			$this->setField('alias', $this->id);
			$this->_save();
		}
		
		$this->_saveLngParts();
	}
	
	/** СОХРАНЕНИЕ ЯЗЫКОВЫХ ЧАСТЕЙ */
	public function _saveLngParts(){
		
		$db = db::get();
		foreach($this->validLngParts as $l => $data){
			
			if($this->isNewlyCreated){
				$data['page_id'] = $this->id;
				$db->insert('pages_'.$l, $data);
			}else{
				$db->update('pages_'.$l, $data, 'page_id='.$this->id);
			}
		}
	}
	
	// ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ
	public function beforeDisplay($data){
		
		$data['modif_date']  = YDate::loadTimestamp($data['modif_date'])->getStrDateShortTime();
		$data['create_date'] = YDate::loadTimestamp($data['create_date'])->getStrDateShortTime();
		$data['type_str']    = self::getPageTypeTitle($data['type']);
		return $data;
	}
	
	// ОПУБЛИКОВАТЬ СТРАНИЦУ
	public function publish(){
	
		$this->setField('published', '1');
		$this->_save();
	}
	
	// СКРЫТЬ СТРАНИЦУ
	public function unpublish(){
	
		$this->setField('published', '0');
		$this->_save();
	}
	
	public function dbGetRow(){
		
		$db = db::get();
		$lng = Lng::get();
		
		$fields = array();
		foreach(self::$lngFields as $f)
			$fields[] = 'COALESCE(lng.'.$f.', lng_default.'.$f.', \'\') AS '.$f;
		
		$sql = '
			SELECT p.*, '.implode(',', $fields).' FROM '.self::TABLE.' p
			LEFT JOIN pages_'.$lng->getCurLng().' lng ON lng.page_id = p.id
			LEFT JOIN pages_'.Lng::$defaultLng.' lng_default ON lng_default.page_id = p.id
			WHERE id='.$db->qe($this->id);
		
		return $db->getRow($sql);
	}
	
	/** ПРОВЕРИТЬ, ЯВЛЯЕТСЯ ЛИ СТРАНИЦА ФРАГМЕТНОМ */
	public function isChunk(){
		
		return $this->getField('type') == self::TYPE_CHUNK;
	}
	
	public static function getPageTypeTitle($type){
		
		switch($type) {
			case self::TYPE_FULL: return 'Основная';
			case self::TYPE_CHUNK: return 'Фрагмент';
			default: trigger_error('Неизвестный тип страницы: '.$type, E_USER_ERROR);
		}
	}

	public static function getHelpIcon($lngKey){
		
		$text = Lng::get($lngKey);
		return '<img src="" alt="" title="'.$text.'">';
	}
}

class PageCollection extends GenericObjectCollection{
	
	// поля, по которым возможна сортировка коллекции
	// каждый ключ должен быть корректным выражением для SQL ORDER BY
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'title' => 'Заголовок',
		'alias' => 'Псевдоним',
		'type' => 'Тип',
		'author' => 'author',
		'published' => 'Публикация',
		'modif_date' => 'Последнее изменение',
	);
	
	
	// ТОЧКА ВХОДА В КЛАСС
	public static function Load(){
			
		$instance = new PageCollection();
		return $instance;
	}
	
	// ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ
	public function getPaginated(){
		
		$db = db::get();
		$lng = Lng::get();
		
		$fields = array();
		foreach(Page::$lngFields as $f)
			$fields[] = 'COALESCE(lng.'.$f.', lng_default.'.$f.', \'\') AS '.$f;
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array(
			'p.*, '.implode(',', $fields),
			'FROM '.Page::TABLE.' p
			LEFT JOIN pages_'.$lng->getCurLng().' lng ON lng.page_id = p.id
			LEFT JOIN pages_'.Lng::$defaultLng.' lng_default ON lng_default.page_id = p.id
			ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = Page::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>