<?php /* Smarty version 2.6.26, created on 2011-08-28 11:02:48
         compiled from Task/upload_files.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Task/upload_files.tpl', 4, false),array('function', 'lng', 'Task/upload_files.tpl', 65, false),)), $this); ?>
<script type="text/javascript"><?php echo '

function getTaskFiles(){
	$.get(\''; ?>
<?php echo SmartyPlugins::function_a(array('href' => "task/get-task-files/".((isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : ''))), $this);?>
<?php echo '\', function(response){
		
		if(response.error){
			$(\'#task-uploaded-files-container\').empty().html(\'<div style="color: red;">\' + response.error + \'</div>\');
			return;
		}
		
		var tbl = $(\'<table class="task-uploaded-files-list" />\');
		var hasNordujob = false;
		var delLink = null;
		for(var i in response.data){
		
			type = \'\';
			if(response.data[i] == \'nordujob\'){
				type = \'nordujob файл\';
				hasNordujob = true;
			}
			else if(/\\.fds$/.test(response.data[i]))
				type = \'файл модели\';
				
			delLink = $(\'<a href="#" class="small" onclick="deleteTaskFile(\\\'\' + response.data[i] + \'\\\'); return false;">удалить</a>\');
			
			tbl.append(
				$(\'<tr />\')
					.append(\'<td>\' + (type ? \'<span class="small" style="color: #888;">\' + type + \'</span>\' : \'\') + \'</td>\')
					.append(\'<td>\' + response.data[i] + \'</td>\')
					.append($(\'<td></td>\').append(delLink)));
		}
		
		$(\'#task-uploaded-files-container\').empty().append(tbl);
		
		if(hasNordujob){
			$(\'#task-uploaded-files-comment\').html(\'<span class="small green">Файл nordujob загружен</span>\');
			$(\'#task-run-button\').show();
		}else{
			$(\'#task-uploaded-files-comment\').html(\'<span class="small red">Файл nordujob не загружен</span>\');
			$(\'#task-run-button\').hide();
		}
			
	}, \'json\');
}

function deleteTaskFile(name){

	if(!confirm(\'Удалить файл "\' + name + \'"?\'))
		return;
		
	$.post(\'?r=task/delete-task-file/'; ?>
<?php echo (isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : ''); ?>
<?php echo '\', {file: name}, function(response){
		if(response != \'ok\')
			alert(response);
		getTaskFiles();
	});
}

$(function() {

	getTaskFiles();
	
});
'; ?>
</script>

<h2><?php echo SmartyPlugins::function_lng(array('snippet' => 'upload_files.uploadfilemsg'), $this);?>
</h2>

<div class="task-uploaded-files">
	<div style="font-weight: bold; text-align: center; margin-bottom: 10px;"><?php echo SmartyPlugins::function_lng(array('snippet' => 'upload_files.uploadfiles'), $this);?>
</div>
	<form action="" method="post" enctype="multipart/form-data">
		<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

		<input type="hidden" name="action" value="task/upload-file" />
		<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : ''); ?>
" />
		<input type="file" name="Filedata" />
		<input type="submit" value="Отправить" />
	</form>
</div>

<div class="task-uploaded-files">
	<div style="font-weight: bold; text-align: center; margin-bottom: 10px;"><?php echo SmartyPlugins::function_lng(array('snippet' => 'upload_files.sendfileslist'), $this);?>
</div>
	<div id="task-uploaded-files-container"></div>
	<div id="task-uploaded-files-comment" style="margin-top: 1em;"></div>

</div>
<div class="paragraph">
	<span id="task-run-button" style="display: none;"><?php echo SmartyPlugins::function_a(array('href' => "task/xrsl-edit/".((isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : '')),'class' => 'button','text' => "Перейти к запуску"), $this);?>
</span>
	<?php echo SmartyPlugins::function_a(array('href' => 'task','class' => 'button','text' => "Вернуться к списку задач"), $this);?>

</div>
