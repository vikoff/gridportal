<html>
<head>
	<title>мега-мастер</title>
	<base href="<?= WWW_ROOT; ?>" />
	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/frontend.css" type="text/css" />
</head>
<body style="width: 100%; height: 100%; margin: 0; padding: 0;">

<form action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="action" value="task-set/save-constructor" />
	<input type="hidden" name="id" value="<?= $vars['instanceId']; ?>" />
	<input type="hidden" name="file" value="<?= $vars['fname']; ?>" />
	
	<table style="width: 100%; height: 100%;" border>
	<tr>
		<td style="overflow: auto; text-align: center; vertical-align: middle;">
			<? if (!empty($vars['formData'])): ?>
				<table class="std-grid narrow">
					<? foreach($vars['formData'] as $row): ?>
					<tr style="vertical-align: top;">
						<td><?= $row['field']; ?></td>
						<td>
							<input type="hidden" name="items[<?= $row['row']; ?>][pre_text]" value="<?= htmlspecialchars($row['pre_text']); ?>" />
							<input type="hidden" name="items[<?= $row['row']; ?>][post_text]" value="<?= htmlspecialchars($row['post_text']); ?>" />
							<input type="text" name="items[<?= $row['row']; ?>][value]" value="<?= htmlspecialchars($row['value']); ?>" />
						</td>
						<td>
							<? if ($row['allow_multiple']): ?>
								<input type="button" name="" value="+" />
							<? endif; ?>
						</td>
					</tr>
					<? endforeach; ?>
				</table>
			<? else: ?>
				Нет данных для редактирования
			<? endif; ?>
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