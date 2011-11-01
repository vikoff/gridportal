<?php /* Smarty version 2.6.26, created on 2011-10-18 20:57:54
         compiled from Task/upload_files.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'lng', 'Task/upload_files.tpl', 16, false),array('function', 'a', 'Task/upload_files.tpl', 36, false),)), $this); ?>
<script type="text/javascript"><?php echo '
$(function() {

	var fm = new FileManager(
		\'task/get-task-files/'; ?>
<?php echo (isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : ''); ?>
<?php echo '\',
		\'task/delete-task-file/'; ?>
<?php echo (isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : ''); ?>
<?php echo '\',
		$(\'#task-uploaded-files-container\'),
		$(\'#task-uploaded-files-comment\')
	);
	
	fm.getFiles();
	
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
