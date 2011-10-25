<?php /* Smarty version 2.6.26, created on 2011-09-03 17:39:57
         compiled from Task/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'lng', 'Task/edit.tpl', 24, false),array('function', 'a', 'Task/edit.tpl', 31, false),)), $this); ?>

<form id="edit-form" action="" method="post">
	<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

	<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />
	
	<h2>
		<?php if (! (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
			Создание новой задачи
		<?php else: ?>
			Редактирование задачи #<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>

		<?php endif; ?>
	</h2>
	
	<div class="paragraph">
		Название:
		<input type="text" name="name" value="<?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
" />
	</div>
	
	<?php if (! (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
	<div class="paragraph">
		<input id="checkbox-is_test" type="checkbox" name="is_test" value="1" <?php if ((isset($this->_tpl_vars['is_test']) ? $this->_tpl_vars['is_test'] : '')): ?>checked="checked"<?php endif; ?> />
		<label for="checkbox-is_test">Тестовая задача</label> 
        
        <img src="images/help.png" align="justify" alt="<?php echo SmartyPlugins::function_lng(array('snippet' => 'edit.help-alt'), $this);?>
" title="<?php echo SmartyPlugins::function_lng(array('snippet' => 'edit.help-test-task'), $this);?>
" width=20 height=20> 
        
	</div>
	<?php endif; ?>
	
	<div class="paragraph">
		<input class="button" type="submit" name="action[task/save][]" value="<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>Сохранить<?php else: ?>Продолжить<?php endif; ?>" />
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "task/list"), $this);?>
">отмена</a>
	</div>
</form>

<script type="text/javascript"><?php echo '

$(function(){

});

'; ?>
</script>