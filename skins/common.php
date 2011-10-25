<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
     "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>

	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?=$this->_getHtmlTitle();?></title>
	<base href="<?=$this->_getHtmlBaseHref();?>" />
	
	<meta name="robots" content="index, follow" />
	<meta name="keywords" />
	<meta name="author" content="Yurij Novikov" />
	<meta name="description" content="description words" />
	<meta name="generator" content="vik-off-CMF" />

<?=$this->_getHtmlLinkTags();?>

	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/frontend.css" type="text/css" />
	<link rel="stylesheet" href="css/smoothness/jquery-ui-1.8.16.custom.css" type="text/css" />
	<!-- <link rel="icon" type="image/png" href="favicon.ico" /> -->
	
	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.pack.js"></script>
	<script type="text/javascript" src="js/jquery.defaultval.min.js"></script>
	<script type="text/javascript" src="js/jquery.ctrlentersend.min.js"></script>
	<script type="text/javascript" src="js/autoresize.jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.browser.min.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
	
</head>
<body>

<div id="site-container">

	<div id="body">
		
		<?=$this->_getHtmlContent();?>
	</div>
	
	<div class="clear"></div>
	
	<div id="footer-container"></div>
	
</div>

<div id="footer">
	<?=$this->_getClientStatisticsLoader();?>
	<div style="margin-top: 10px; font-size: 11px;"><?=$this->_getPhpPageStatistics();?></div>
	project base
</div>


</body>
</html>