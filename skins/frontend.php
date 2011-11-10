<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
     "http://www.w3.org/TR/html4/strict.dtd"><html>
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
		css/smoothness/jquery-ui-1.8.16.custom.css" type="text/css" />
	
	<!--link rel="stylesheet" href="css/smoothness/jquery-ui-1.8.16.custom.css" type="text/css" />
	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/frontend.css" type="text/css" />
	<link rel="stylesheet" href="css/simplemodal.css" type="text/css" />
	<!-- <link rel="icon" type="image/png" href="favicon.ico" /> -->
	
	<script type="text/javascript">
		var WWW_ROOT = '<?= WWW_ROOT; ?>';
		var CUR_LNG = '<?= Lng::get()->getCurLng(); ?>';
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
	
	<!--script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.pack.js"></script>
	<script type="text/javascript" src="js/jquery.ctrlentersend.min.js"></script>
	<script type="text/javascript" src="js/jquery.browser.min.js"></script>
	<script type="text/javascript" src="js/jquery.address-1.4.min.js"></script>
	<script type="text/javascript" src="js/jquery.simplemodal.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/frontend.js"></script-->
	
</head>
<body>

<div id="site-container">

	<div id="header">
		<?=$this->getLanguageBlock();?>
		<br><br>
		<?=$this->getLoginBlock();?>
		<div id="logo"><h1><?=Lng::get('top.title');?> <span style="font-size: 11px;"><?=Lng::get('top.version');?> 0.1</span></h1></div>
	</div>
	
	<?/*=$this->getTopMenu(); */?>
	<div id="top-menu">
		<?=$this->_getTopMenuHTML(); ?>
	</div>
	
	<?=$this->_getUserMessages();?>
	
	<div id="body">
		
		<?=$this->_getHtmlContent();?>
	</div>
	
	<div class="clear"></div>
	
	<div id="footer-container"></div>
	
</div>

<div id="footer">
	<?=$this->_getClientStatisticsLoader();?>
</div>

</body>
</html>