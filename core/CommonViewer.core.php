<?php
/**
 * 
 * 
 * @using:
 * 		const: CFG_SITE_NAME, FS_ROOT, WWW_ROOT
 * 
 */
class CommonViewer{
	
	protected $_skin = 'common';
	protected $_tplPath = 'templates';
	
	protected $_htmlTitle = '';
	protected $_htmlLinkTags = array();
	protected $_breadcrumbs = array();
	protected $_isAutoBreadcrumbsAdded = FALSE;
	protected $_htmlContent = '';
	
	private static $_instance = null;
	
	
	// ТОЧКА ВХОДА В КЛАСС (ПОЛУЧИТЬ ЭКЗЕМПЛЯР CommonViewer)
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new CommonViewer();
		
		return self::$_instance;
	}
	
	// КОНСТРУКТОР
	protected function __construct(){
		
		$this->_tplPath = FS_ROOT.$this->_tplPath.'/';
	}
	
	public function setTitle($title){
		
		$this->_htmlTitle = $title;
		return $this;
	}
	
	public function prependTitle($title, $separator = ' - '){
		
		$this->_htmlTitle = strlen($this->_htmlTitle)
			? $title.$separator.$this->_htmlTitle
			: $title.$this->_htmlTitle;
		return $this;
	}
	
	public function appendTitle($title, $separator = ' - '){
		
		$this->_htmlTitle = strlen($this->_htmlTitle)
			? $this->_htmlTitle.$separator.$title
			: $this->_htmlTitle.$title;
		return $this;
	}
	
	public function setLinkTags($tags){
	
		foreach($tags as $tagname => $tagval)
			if(strlen($tagval))
				$this->_htmlLinkTags[$tagname] = $tagval;
				
		return $this;
	}
	
	public function setBreadcrumbs($mode, $data = array()){
		
		$appendData = FALSE;
		
		switch($mode){
			case 'auto':
				$this->setBreadcrumbsAuto();
				break;
			case 'auto-with':
				$this->setBreadcrumbsAuto();
				$appendData = TRUE;
				break;
			case 'set':
				$this->setBreadcrumbsAuto();
				$this->_breadcrumbs = array();
				$appendData = TRUE;
				break;
			case 'add':
				$appendData = TRUE;
				break;
			default: trigger_error('Неверный режим установки breadcrumbs. Допустимые значения: "set", "add", "auto", "auto-with"', E_USER_ERROR);
		}
		
		if($appendData){
			if(count($data) && !is_array($data[0])){
				$this->_breadcrumbs[] = $data;
			}else{
				foreach($data as $k => $v)
					$this->_breadcrumbs[] = $v;
			}
		}
		return $this;
	}
	
	public function setBreadcrumbsAuto(){
		
		if($this->_isAutoBreadcrumbsAdded)
			return;
			
		$this->_isAutoBreadcrumbsAdded = TRUE;
		$this->_breadcrumbs = array();
	}
	
	public function setContent($content){
	
		$this->_htmlContent = $content;
		return $this;
	}
	
	/** ПОЛУЧИТЬ КОНТЕНТ ИЗ ПРОИЗВОЛЬНОГО ФАЙЛА (БЕЗ ИНТЕРПРЕТАЦИИ) */
	public function setContentHtmlFile($file){
		
		$this->_htmlContent .= $this->getContentHtmlFile($file);
		return $this;
	}
	
	/** ПОЛУЧИТЬ КОНТЕНТ ИЗ PHP-ФАЙЛА (С ИНТЕРПРЕТАЦИЕЙ) */
	public function setContentPhpFile($file, $variables = array()){
		
		$this->_htmlContent .= $this->getContentPhpFile($file, $variables);
		return $this;
	}
	
	public function setContentSmarty($template, $variables = array()){
		
		$smarty = App::smarty();
		$smarty->assign($variables);
		$this->_htmlContent = $smarty->fetch($template);
		$smarty->clear_all_assign();
		return $this;
	}
	
	protected function _getHtmlTitle(){
		
		return strlen($this->_htmlTitle)
			? $this->_htmlTitle.' - '.CFG_SITE_NAME
			: CFG_SITE_NAME;
	}
	
	protected function _getHtmlLinkTags(){
		
		$output = '';
		foreach($this->_htmlLinkTags as $rel => $href)
			$output = "\t".'<link rel="'.$rel.'" href="'.$href.'" />'."\n";
		
		return $output;
	}
	
	/** GET BASE HREF URL */
	protected function _getHtmlBaseHref(){
		
		return WWW_ROOT;
	}
	
	/** GET BREADCRUMBS HTML */
	protected function _getBreadcrumbs(){
		
		$breadcrumbs = array();
		$num = count($this->_breadcrumbs);
		foreach($this->_breadcrumbs as $index => $v)
			$breadcrumbs[] = is_null($v[0]) || ($index + 1) == $num
				? '<span class="item">'.$v[1].'</span>'
				: '<a class="item" href="'.App::href($v[0]).'">'.$v[1].'</a>';
		
		return $num ? '<div class="breadcrumbs">'.implode('<span class="mediator">  » </span>', $breadcrumbs).'</div>' : '';
	}
	
	/** GET USER MESSAGE HTML */
	protected function _getUserMessages(){
	
		return Messenger::get()->getAll();
	}
	
	/** GET HTML CONTENT */
	protected function _getHtmlContent(){
		
		return $this->_htmlContent;
	}
	
	/** GET CLIENT STATISTICS LOADER HTML */
	protected function _getClientStatisticsLoader(){
	
		$uStat = UserStatistics::get();
		
		return $uStat->checkClientSideStatistics()
			? $uStat->getClientSideStatisticsLoader()
			: '';
	}
	
	/** GET PHP PAGE STATISTICS HTML */
	protected function _getPhpPageStatistics(){
		
		$scriptExecutionTime = round(microtime(1) - $GLOBALS['__vikOffTimerStart__'], 4);
		
		$output = ''
			.'<table class="php-page-statistics">'
			.'<tr class="section"><th colspan="2" >PHP</th></tr>'
			.'<tr><th>Показатель</th><th>Значение</th></tr>'
			.'<tr><td>Версия интерпретатора</td><td>'.phpversion().'</td></tr>'
			.'<tr><td>Время выполнения скрипта</td><td>'.$scriptExecutionTime.' сек.</td></tr>'
			.'<tr><td>Подключенных файлов</td><td>'.count(get_included_files()).'</td></tr>'
			.'<tr><td>Пик использования памяти</td><td>'.Common::formatByteSize(memory_get_peak_usage()).'</td></tr>'
			
			.'<tr class="section"><th colspan="2" >SQL</th></tr>'
			.'<tr><th>Запрос</th><th>Время, сек</th></tr>'
		;
		foreach(db::get()->getQueriesWithTime() as $q)
			$output .= '<tr><td>'.$q['sql'].'</td><td>'.round($q['time'], 5).'</td></tr>';
		$output .= ''
			.'<tr class="b"><td>Всего запросов</td><td>'.db::get()->getQueriesNum().'</td></tr>'
			.'<tr class="b"><td>Общее время выполнения</td><td>'.round(db::get()->getQueriesTime(), 5).' сек.</td></tr>'
			.'</table>'
		;
		
		$output = '
			<script type="text/javascript">
			$(function(){
				VikDebug.print(\''.preg_replace(array("/\s+/", "/'/"), array(" ", "\\'"), $output).'\', "performance", {activateTab: false, onPrintAction: "none"});
			});
			</script>';
		return $output;
	}
	
	/** RENDER ALL */
	public function render($boolReturn = FALSE){
		
		// сохранение пользовательской статистики
		UserStatistics::get()->savePrimaryStatistics();
		
		if($boolReturn)
			ob_start();
			
		include(FS_ROOT.'skins/'.$this->_skin.'.php');
		
		if($boolReturn)
			return ob_get_clean();
		else
			return $this;
	}
	
	/** RENDER ERROR PAGE */
	public function error($message = ''){
		
		if(AJAX_MODE){
			echo $message;
		}else{
			$this
				->setTitle('Ошибка')
				->setContentPhpFile('error.php', array('message' => $message))
				->render();
		}
		exit();
	
		$variables = array(
			'message' => $message,
		);
		$this
			->setTitle('Ошибка')
			->setContentPhpFile('error.php', $variables)
			->render();
		exit();
	}
	
	/** RENDER ERROR 403 PAGE */
	public function error403($message = ''){
	
		header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden'); // 'HTTP/1.1 403 Forbidden'
		
		if(AJAX_MODE){
			echo $message;
		}else{
			$this
				->setTitle('Доступ запрещен')
				->setContentPhpFile('403.php', array('message' => $message))
				->render();
		}
		exit();
	}
	
	/** RENDER ERROR 404 PAGE */
	public function error404($message = ''){
	
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); // 'HTTP/1.1 404 Not Found'
		
		if(AJAX_MODE){
			echo $message;
		}else{
			$this
				->setTitle('Страница не найдена')
				->setContentPhpFile('404.php', array('message' => $message))
				->render();
		}
		exit();
	}
	
	/** АКСЕССОР ДЛЯ ШАБЛОНОВ */
	public function __get($name){
		
		return isset($this->$name) ? $this->$name : '';
	}
	
	/** ПОЛУЧИТЬ СОДЕРЖИМОЕ HTML ФАЙЛА */
	public function getContentHtmlFile($file){
		
		return file_get_contents($this->_tplPath.$file);
	}
	
	/** ПОЛУЧИТЬ ПРОИНТЕРПРЕТИРОВАННОЕ СОДЕРЖИМОЕ PHP ФАЙЛА */
	public function getContentPhpFile($file, $variables = array()){
		
		extract($variables);
		
		foreach($variables as $k => $v)
			$this->$k = $v;
			
		ob_start();
		include($this->_tplPath.$file);
		
		foreach($variables as $k => $v)
			unset($this->$k);
		
		return ob_get_clean();
	}
}

?>