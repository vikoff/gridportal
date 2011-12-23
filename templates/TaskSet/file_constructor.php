<html>
<head>
	<title>мега-мастер</title>
	<base href="<?= WWW_ROOT; ?>" />
	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/frontend.css" type="text/css" />
	<script type="text/javascript" src="http://scripts.vik-off.net/debug.js"></script>
	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
</head>
<body style="width: 100%; height: 100%; margin: 0; padding: 0; background: #eee;">

<style type="text/css">
table.multiplier tr{
	border: none;
}
table.multiplier td{
	border: none;
	padding: 1px 5px;
}
input.mult{
	width: 100%;
}
input.mult.from{
	width: 40px;
}
input.mult.to{
	width: 40px;
}
input.mult.step{
	width: 30px;
}
</style>
<script type="text/javascript">
var Multiplier = {
	
	/** создать интервал */
	interval: function(id, index){
		
		$('#m-interval-' + id + '-' + index).hide();
		$('#m-rminterval-' + id + '-' + index).show();
		
		var td = $('#m-combination-'+id+'-'+index);
		var val = td.find('input.mult').val();
		
		this._createIntervalTd(id, index, {from: val, to: val, step: 1}, td);
	},
	
	/** удалить интервал (оставить одиночное значение) */
	removeInterval: function(id, index){
		
		$('#m-interval-' + id + '-' + index).show();
		$('#m-rminterval-' + id + '-' + index).hide();
		
		var td = $('#m-combination-'+id+'-'+index);
		var val = td.find('input.mult.from').val();
		
		td
			.empty()
			.append('<input type="text" class="mult" name="items['+id+'][value]['+index+'][single]" value="' + val + '" />')
			
	},
	
	/** добавить комбинацию */
	add: function(id, value){
		
		var parent = $('#multiplier-' + id);
		
		// получение индекса (номер варианта)
		var index = parent.data('counter');
		parent.data('counter', index + 1);
		
		var valTd = typeof value == 'string'
			? this._createSingleValueTd(id, index, value)
			: this._createIntervalTd(id, index, value);
			
		$('<tr />')
			.append('<td>Вариант ' + (index + 1) + ':</td>')
			.append(valTd)
			.append($('<td style="text-align: right;" />')
				.append('<a href="#" id="m-interval-'+id+'-'+index+'" onclick="Multiplier.interval('+id+', '+index+'); return false;" title="задать интервал значений">интервал</a>')
				.append('<a href="#" id="m-rminterval-'+id+'-'+index+'" onclick="Multiplier.removeInterval('+id+', '+index+'); return false;" title="убрать интервал, задать одиночное значение" style="display: none;"><strike>интервал</strike></a>'))
			.append($('<td style="text-align: right;" />')
				.append(index == 0 ? '<span />' : '<input type="button" onclick="Multiplier.remove('+id+', '+index+')" value="-" title="Убрать значение" />'))
			.appendTo(parent);
	},
	
	/** удалить кобминацию */
	remove: function(id, index){
		
		$('#m-combination-'+id+'-'+index).parent().remove();
	},
	
	/** создание ячейки с одиночным значением */
	_createSingleValueTd: function(id, index, value){
		
		return $('<td id="m-combination-'+id+'-'+index+'" style="text-align: right; width: 200px;" />')
			.append('<input type="text" class="mult" name="items['+id+'][value]['+index+'][single]" value="' + value + '" />')
	},
	
	/** создание ячейки с интервалом */
	_createIntervalTd: function(id, index, value, td){
		
		if (td)
			td.empty();
		else
			td = $('<td id="m-combination-'+id+'-'+index+'" style="text-align: right; width: 200px;" />');
			
		td
			.append('<span>от: </span>')
			.append('<input class="mult from" type="text" name="items['+id+'][value]['+index+'][from]" value="' + value['from'] + '" />')
			.append('<span> до: </span>')
			.append('<input class="mult to" type="text" name="items['+id+'][value]['+index+'][to]" value="' + value['to'] + '" />')
			.append('<span> шаг: </span>')
			.append('<input class="mult step" type="text" name="items['+id+'][value]['+index+'][step]" value="' + value['step'] + '" />');
		
		return td;
	}
}

$(function(){
	$('table.multiplier').data('counter', 0);
});

</script>


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
					<tr style="vertical-align: top !important;">
						<td><?= $row['field']; ?></td>
						<td>
							<input type="hidden" name="items[<?= $row['row']; ?>][pre_text]" value="<?= htmlspecialchars($row['pre_text']); ?>" />
							<input type="hidden" name="items[<?= $row['row']; ?>][post_text]" value="<?= htmlspecialchars($row['post_text']); ?>" />
							<input type="hidden" name="items[<?= $row['row']; ?>][field]" value="<?= htmlspecialchars($row['field']); ?>" />
							
							<? if ($row['allow_multiple']): ?>
							
								<table class="multiplier" id="multiplier-<?= $row['row']; ?>"></table>
								
								<script type="text/javascript">
								$(function(){
									<? if (is_array($row['value'])): ?>
									
										<? foreach($row['value'] as $r): ?>
										
											Multiplier.add(
												<?= $row['row']; ?>,
												<? if (is_array($r)): ?>
													{
														from: "<?= htmlspecialchars($r['from']) ?>",
														to: "<?= htmlspecialchars($r['to']) ?>",
														step: "<?= htmlspecialchars($r['step']) ?>"
													}
												<? else: ?>
													'<?= htmlspecialchars($r); ?>'
												<? endif; ?>
											);
											
										<? endforeach; ?>
										
									<? else: ?>
									
										Multiplier.add(<?= $row['row']; ?>, '<?= htmlspecialchars($row['value']); ?>');
										
									<? endif; ?>
								});
								</script>
								
							<? else: ?>
								<input type="text" name="items[<?= $row['row']; ?>][value]" value="<?= htmlspecialchars($row['value']); ?>" />
							<? endif; ?>
						</td>
						<td style="vertical-align: bottom;">
							<? if ($row['allow_multiple']): ?>
								<input onclick="Multiplier.add(<?= $row['row']; ?>, '');" type="button" value="+" title="Добавить еще одно значение" />
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
