<html>
<head>
	<title><?= Lng::get('tast-set-edit-file.mega-workflow-editor'); ?></title>
	<base href="<?= WWW_ROOT; ?>" />
	<link rel="stylesheet" href="css/fileeditor.css" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
</head>
<body class="file-editor fds-file">
	<form name="editor" action="" method="post">
		<?= FORMCODE; ?>
		<input type="hidden" name="action" value="task-set/save-constructor" />
		<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
		<input type="hidden" name="file" value="<?= $this->fname; ?>" />
		<div id="main">
			<div id="caption">
				<?= $this->fname; ?>
				<div id="toolbar">
					<span class="file-editor-save" title="<?= Lng::get('save'); ?>"></span>&nbsp;
					<span class="file-editor-help" title="<?= Lng::get('help'); ?>"></span>
				</div>
			</div>
			<div id="content">
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
				.append('<a href="#" id="m-interval-'+id+'-'+index+'" onclick="Multiplier.interval('+id+', '+index+'); return false;" title="задать интервал значений"><?= Lng::get('tast-set-file-constructor-interval'); ?></a>')
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
		
		return $('<td id="m-combination-'+id+'-'+index+'" style="text-align: right; width: 220px;" />')
			.append('<input type="text" class="mult" name="items['+id+'][value]['+index+'][single]" value="' + value + '" />')
	},
	
	/** создание ячейки с интервалом */
	_createIntervalTd: function(id, index, value, td){
		
		if (td)
			td.empty();
		else
			td = $('<td id="m-combination-'+id+'-'+index+'" style="text-align: right; width: 220px;" />');
			
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
			<? include($this->formFile); ?>
			</div>
			<div id="bottom">
				<div id="bottom-left">
					
				</div>
				<div id="bottom-right">
					<!--Тип файла: файл модели FDS&nbsp;&nbsp;&nbsp;
					Размер файла: 1.8 Мб&nbsp;&nbsp;&nbsp;
					Вариантов: 16&nbsp;&nbsp;&nbsp;
					Общий размер: 28.8 Мб-->
				</div>
				<div class="cl"></div>
			</div>
			<div id="help">
				<div id="help-content">Some help will be here</div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		<? if ($this->saved_success): ?>
			$('#bottom-left').text('<?= Lng::get('saved'); ?>');
		<? endif; ?>
		$('.file-editor-save').click(function(){
			document.forms['editor'].submit();
		});
		$('.file-editor-help').click(function(){
			$('#help').slideToggle();
		});
		$('.fds-file-param-spoiler').click(function(){
			$('.fds-file-spoiler-' + $(this).attr('spoiler')).slideToggle();
		});
		$('.fds-file-param-delete').click(function(){
			var $value = $(this).parent().parent();
			$value.slideUp(function(){
				$value.remove();
			});
		});
		$('.fds-file-arg-add').click(function(){
			$('<div class="fds-file-arg-value"><div class="fds-file-arg-value-single"><input type="text" name="items[][value][][single]" value="" /></div><div class="fds-file-arg-value-options"><span class="fds-file-param-range">  </span><span class="fds-file-param-delete">  </span>	</div><div class="cl"></div></div>')
				.css({ display: 'none' })
				.appendTo($(this).parent())
				.slideDown();
		});
	</script>
</body>
</html>
