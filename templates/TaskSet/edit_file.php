<?

// echo '<pre>'; print_r($vars); die;

?>
<html>
<head>
	<title>мега-редактор</title>
	<base href="<?= WWW_ROOT; ?>" />
	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/frontend.css" type="text/css" />
</head>
<body style="width: 100%; height: 100%; margin: 0; padding: 0;">

<form action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="action" value="task-set/save-file" />
	<input type="hidden" name="id" value="<?= $vars['instanceId']; ?>" />
	<input type="hidden" name="file" value="<?= $vars['fname']; ?>" />
	
	<table style="width: 100%; height: 100%;">
	<tr>
		<td>
			<textarea name="content" style="width: 100%; height: 100%; padding: 10px;" spellcheck="false"><?= htmlspecialchars($vars['content']); ?></textarea>
		</td>
	</tr>
	<tr style="height: 30px; border; solid 1px black; text-align: center;">
		<td style="font-weight: bold;"><?= $vars['fname']; ?></td>
	</tr>
	<tr style="height: 50px; border; solid 1px black; text-align: center;">
		<td>
			<input type="submit" class="button" value="Сохранить" />
			<a href="#" class="button" onclick="if(confirm('Выйти?')){window.parent.$.modal.close();} return false;">Закрыть</a>
		</td>
	</tr>
	</table>
</form>
	
</body>
</html>