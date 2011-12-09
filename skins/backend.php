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

	<link rel="stylesheet" href="css/redmond/jquery-ui-1.8.16.custom.css" type="text/css" />
	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/backend.css" type="text/css" />
	<!-- <link rel="icon" type="image/png" href="favicon.ico" /> -->
	
	<script type="text/javascript">
		var WWW_ROOT = '<?= WWW_ROOT; ?>';
		var CUR_LNG = '<?= Lng::get()->getCurLng(); ?>';
	</script>
	<script type="text/javascript" src="
		js/jquery-1.6.2.min.js,
		js/jquery-ui-1.8.16.custom.min.js,
		js/jquery.ctrlentersend.min.js,
		js/jquery.floatblock.js,
		js/autoresize.jquery.min.js,
		js/common.js,
		js/backend.js,
		js/jquery.simplemodal.js"></script>
		<script type="text/javascript" src="http://scripts.vik-off.net/debug.js"></script>
	
</head>
<body>

<div id="site-container">

	<div id="top">
		<div style="float: left; padding: 10px; background-color: #FFD46D;"><strong><?=CFG_SITE_NAME;?></strong> + логотип</div>
		<div style="text-align: right;"><a href="<?App::href('');?>">На сайт</a></div>
		
		<div id="top-menu-list">
			<?=$this->_getTopMenu();?>
		</div>
		
		<div class="clear"></div>
	</div>
	
	<table id="body-frame">
	<tbody>
	<tr>
		<td id="body-left">
		
			<div id="left-menu-container">
				<div id="left-menu-list">
					<?=$this->_getLeftMenu();?>
				</div>
			</div>
			
		</td>
		<td id="body-right">
			
			<?=$this->_getBreadcrumbs(); ?>
			
			<?=$this->_getUserMessages();?>
			
			<?=$this->_getHtmlContent();?>
	
		</td>
	</tr>
	</tbody>
	</table>
	
	<div class="clear"></div>
	
	<div id="footer-container"></div>
	
</div>

<div id="footer">
	project base
</div>

</body>
</html>