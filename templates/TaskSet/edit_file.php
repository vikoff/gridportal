<!DOCTYPE html>
<html>
<head>
	<title><?= Lng::get('tast-set-edit-file.mega-workflow-editor'); ?></title>
	<base href="<?= WWW_ROOT; ?>" />
	<link rel="stylesheet" href="css/fileeditor.css" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
</head>
<body class="file-editor text-file">
	<form name="editor" action="" method="post">
		<?= FORMCODE; ?>
		<input type="hidden" name="action" value="task-set/save-file" />
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
				<textarea id="main-editor" name="content" spellcheck="false"><?= htmlspecialchars($this->content); ?></textarea>
			</div>
			<div id="bottom">
				<div id="bottom-left">
					
				</div>
				<div id="bottom-right">
					Размер файла: <?= $this->file_size; ?>&nbsp;&nbsp;&nbsp;
					<!--Тип файла: файл модели FDS&nbsp;&nbsp;&nbsp;
					Вариантов: 16&nbsp;&nbsp;&nbsp;
					Общий размер: 28.8 Мб-->
				</div>
				<div class="cl"></div>
			</div>
			<div id="help">
				<div id="help-content">Some help will be here</div>
			</div>
			<div id="save">
				<div id="save-message"><?= Lng::get('saved'); ?></div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		<? if ($this->saved_success): ?>
			$('#save').show();
			setTimeout(function(){
				$('#save').fadeOut(2000);
			}, 4000);
		<? endif; ?>
		$('.file-editor-save').click(function(){
			document.forms['editor'].submit();
		});
		$('.file-editor-help').click(function(){
			$('#help').slideToggle({ step: function(){ $('#content').css({ paddingTop: 20 + $(this).height() }) } });
		});
	</script>
</body>
</html>