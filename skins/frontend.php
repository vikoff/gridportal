<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
     "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?=$this->_getHtmlTitle();?></title>
	<base href="<?=$this->_getHtmlBaseHref();?>" />
	
	<meta name="robots" content="index, follow" />
	<meta name="keywords" />
	<meta name="author" content="CrimeaEco VO" />
	<meta name="description" content="description words" />
	<meta name="generator" content="GridPortal" />

	<?=$this->_getHtmlLinkTags();?>

	<link rel="stylesheet" href="
		css/common.css,
		css/frontend.css,
		css/simplemodal.css,
		css/redmond/jquery-ui-1.8.16.custom.css" type="text/css" />
	
	<link rel="icon" type="image/png" href="images/favicon.ico" />
	
	<script type="text/javascript">
		var WWW_ROOT = '<?= WWW_ROOT; ?>';
		var CUR_LNG = '<?= Lng::get()->getCurLng(); ?>';
		
		/**
		 * языковые фрагменты
		 * @example Lng.taskSetUpdateStr1 (в любом месте js на любой странице)
		 */
		var LNG = {
			taskSetUpdateStr1: '<?= Lng::get('task-set.update.str1'); ?>',
			taskSetUpdateSec: '<?= Lng::get('task-set.update.sec'); ?>',
			taskSetUpdateUpdating: '<?= Lng::get('task-set.update.updating'); ?>',
		};
	</script>
	<script type="text/javascript" src="
		js/jquery-1.6.2.min.js,
		js/jquery-ui-1.8.16.custom.min.js,
		js/jquery.ctrlentersend.min.js,
		js/jquery.browser.min.js,
		js/jquery.address-1.4.min.js,
		js/common.js,
		js/frontend.js,
		js/jquery.simplemodal.js"></script>
	<script type="text/javascript" src="http://scripts.vik-off.net/debug.js"></script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<div id="logo"><!-- <?=Lng::get('top.title');?> --></div>
			<div id="rightHeader">
				<div id="langBlock">
					<?=$this->getLanguageBlock();?>
				</div>
				<div id="profileBlock">
					<?=$this->getLoginBlock();?>
				</div>
			</div>
		</div>
		<div id="menu">
			<div id="menuWrapper">
				<?=$this->_getTopMenuHTML(); ?>
				<div class="cl"></div>
			</div>
			<div class="cl"></div>
		</div>
		<?=$this->_getUserMessages();?>
		<div id="main">
			<?=$this->_getHtmlContent();?>
		</div>
		<div id="footer">
			<a href="<?= App::href('page/developers'); ?>"><?=Lng::get('frontend-developers');?></a>
			<?=$this->_getClientStatisticsLoader();?>
		</div>
		<div id="popup">
			
		</div>
	</div>
<script type="text/javascript">
    var reformalOptions = {
        project_id: 48381,
        project_host: "crimeaecogrid.reformal.ru",
        force_new_window: false,
        tab_alignment: "left",
        tab_top: "300",
        tab_bg_color: "#8ceb9d",
        tab_image_url: "http://tab.reformal.ru/0J7RgdGC0LDQstC40YLRjCDQvtGC0LfRi9Cy/FFFFFF/f5dab822e975a4eb45fcae69ce487412"
    };
    
    (function() {
        //if ('https:' == document.location.protocol) return;
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'http://media.reformal.ru/widgets/v1/reformal.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script>
</body>
</html>