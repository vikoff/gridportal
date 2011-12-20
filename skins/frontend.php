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
			<?=$this->_getClientStatisticsLoader();?>
		</div>
		<div id="popup">
			
		</div>
	</div>
</body>
</html>