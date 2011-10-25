<?php /* Smarty version 2.6.26, created on 2011-09-29 06:52:19
         compiled from Task/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'lng', 'Task/edit.tpl', 8, false),array('function', 'a', 'Task/edit.tpl', 31, false),)), $this); ?>

<form id="edit-form" action="" method="post">
	<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

	<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />
	
	<h2>
		<?php if (! (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
			<?php echo SmartyPlugins::function_lng(array('snippet' => 'edit.addNewTask'), $this);?>

		<?php else: ?>
			<?php echo SmartyPlugins::function_lng(array('snippet' => 'edit.renameTask'), $this);?>
 #<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>

		<?php endif; ?>
	</h2>
	
	<div class="paragraph">
		<?php echo SmartyPlugins::function_lng(array('snippet' => 'edit.name'), $this);?>

		<input type="text" name="name" value="<?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
" />
	</div>
	
	<?php if (! (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
	<div class="paragraph">
		<input id="checkbox-is_test" type="checkbox" name="is_test" value="1" <?php if ((isset($this->_tpl_vars['is_test']) ? $this->_tpl_vars['is_test'] : '')): ?>checked="checked"<?php endif; ?> />
		<label for="checkbox-is_test"><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete-2'), $this);?>
</label> 
        
        <img src="images/help.png" align="justify" alt="<?php echo SmartyPlugins::function_lng(array('snippet' => 'edit.help-alt'), $this);?>
" title="<?php echo SmartyPlugins::function_lng(array('snippet' => 'edit.help-test-task'), $this);?>
" width=20 height=20> 
        
	</div>
	<?php endif; ?>
	
	<div class="paragraph">
		<input class="button" type="submit" name="action[task/<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>rename<?php else: ?>save<?php endif; ?>][]" value="<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?><?php echo SmartyPlugins::function_lng(array('snippet' => 'save'), $this);?>
<?php else: ?><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.get'), $this);?>
<?php endif; ?>" />
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "task/list"), $this);?>
"><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete-5'), $this);?>
</a>
	</div>
</form>

<script type="text/javascript"><?php echo '

$(function(){

});

'; ?>
</script>