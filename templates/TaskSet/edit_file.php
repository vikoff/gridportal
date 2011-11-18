<?

// echo '<pre>'; print_r($vars); die;

?>
<html>
<head>
	<title>мега-редактор</title>
	<base href="<?= WWW_ROOT; ?>" />
	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/frontend.css" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
	<style type="text/css">
		.message-saved{
			display: none;
			position: absolute;
			padding: 5px 10px;
			color: green;
			border: solid 1px green;
			font-weight: bold;
			top: -20px;
			left: -130px;
		}
	</style>
</head>
<body style="width: 100%; height: 100%; margin: 0; padding: 0;">

<form action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="action" value="task-set/save-file" />
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	<input type="hidden" name="file" value="<?= $this->fname; ?>" />
	
	<table style="width: 100%; height: 100%;">
	<tr>
		<td>
			<textarea name="content" style="width: 100%; height: 100%; padding: 10px;" spellcheck="false"><?= htmlspecialchars($this->content); ?></textarea>
		</td>
	</tr>
	<tr style="height: 30px; border; solid 1px black; text-align: center;">
		<td style="font-weight: bold;"><?= $this->fname; ?></td>
	</tr>
	<tr style="height: 50px; border; solid 1px black; text-align: center;">
		<td>
			
			<div style="display: inline-block; position: relative;">
				<div class="message-saved">Сохранено</div>
			</div>
			<input type="submit" class="button" value="Сохранить" />
			<a href="#" class="button" onclick="if(confirm('Выйти?')){window.parent.$.modal.close();} return false;">Закрыть</a>
		</td>
	</tr>
	</table>
</form>
<script type="text/javascript">
	<? if ($this->saved_success): ?>
		$('.message-saved').show();
		setTimeout(function(){ $('.message-saved').fadeOut(1000); }, 1000);
	<? endif; ?>
</script>

</body>
</html>