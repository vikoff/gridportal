<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
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
    <link href="css/style_usab.css" rel="stylesheet">
	<!--[if lte IE 8]> <link href="css/style_usab_ie.css" rel="stylesheet"> <![endif]-->
	
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
	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.address-1.4.min.js"></script>
	<script type="text/javascript" src="
		js/jquery-ui-1.8.16.custom.min.js,
		js/jquery.ctrlentersend.min.js,
		js/jquery.browser.min.js,
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
			<a href="<?= App::href('page/developers'); ?>"><?=Lng::get('frontend-developers');?></a>&nbsp;&nbsp;&nbsp;
			<a href="<?= App::href('page/help'); ?>"><?=Lng::get('frontend-instructions');?></a>
			<?=$this->_getClientStatisticsLoader();?>
		</div>
		<div id="popup">
			
		</div>
	</div>

<!-- <script type="text/javascript">
    var reformalOptions = {
        project_id: 48381,
        project_host: "crimeaecogrid.reformal.ru",
        force_new_window: true,
        tab_alignment: "left",
        tab_top: "300",
        tab_bg_color: "#8ceb9d",
        tab_image_url: "http://tab.reformal.ru/0J7RgdGC0LDQstC40YLRjCDQvtGC0LfRi9Cy/FFFFFF/f5dab822e975a4eb45fcae69ce487412"
	};
    
    (function() {
        var script = document.createElement('script');
        script.type = 'text/javascript'; script.async = true;
        script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v2/reformal.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script>
<script type="text/javascript">
    var reformalOptions = {
        project_id: 48381,
        project_host: "crimeaecogrid.rc1.reformal.ru",
		tab_orientation: "left",
        tab_indent: "300",
        tab_bg_color: "#8ceb9d",
        tab_border_color: "#FFFFFF",
        tab_image_url: "http://tab.reformal.ru/0J7RgdGC0LDQstC40YLRjCDQvtGC0LfRi9Cy/FFFFFF/6cc334dae721477409d18262ee4f19cf/left/0"
    };
    
    (function() {
        var script = document.createElement('script');
        script.type = 'text/javascript'; script.async = true;
        script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v2/reformal.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script> -->


<script type="text/javascript">
reformal_wdg_w    = "713";
reformal_wdg_h    = "560";
reformal_wdg_domain    = "crimeaecogrid";
reformal_wdg_mode    = 0;
reformal_wdg_title   = "Crimea Eco Grid - портал!";
reformal_wdg_ltitle  = "Оставьте свой отзыв";
reformal_wdg_lfont   = "";
reformal_wdg_lsize   = "";
reformal_wdg_color   = "#8CEB9D";
reformal_wdg_bcolor  = "#55cf63";
reformal_wdg_tcolor  = "#FFFFFF";
reformal_wdg_align   = "left";
reformal_wdg_charset = "utf-8";
reformal_wdg_waction = 0;
reformal_wdg_vcolor  = "#9FCE54";
reformal_wdg_cmline  = "#E0E0E0";
reformal_wdg_glcolor  = "#105895";
reformal_wdg_tbcolor  = "#FFFFFF";
reformal_wdg_tcolor_aw4  = "#3F4543";
 
reformal_wdg_bimage = "7688f5685f7701e97daa5497d3d9c745.png";
 
</script>
<script type="text/javascript" language="JavaScript" src="http://rc1.reformal.ru/tabn2v4.js?charset=utf-8"></script><noscript><a href="http://crimeaecogrid.rc1.reformal.ru">Crimea Eco Grid - portal feedback</a> <a href="http://rc1.reformal.ru"><img src="http://widget.rc1.reformal.ru/i/reformal_ru.png" /></a></noscript>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-10659754-14']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-10659754-15']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>