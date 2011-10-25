<?php /* Smarty version 2.6.26, created on 2011-09-29 05:34:50
         compiled from Task/delete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'lng', 'Task/delete.tpl', 3, false),array('function', 'a', 'Task/delete.tpl', 10, false),)), $this); ?>
<p>

	<?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete-1'), $this);?>
 <b><?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
</b> <?php if ((isset($this->_tpl_vars['is_test']) ? $this->_tpl_vars['is_test'] : '')): ?><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete-2'), $this);?>
<?php else: ?><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete-3'), $this);?>
<?php endif; ?>?

	<form action="" method="post">
		<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />
		<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

		
		<input class="button" type="submit" name="action[task/delete]" value="<?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete-4'), $this);?>
" />
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "task/list"), $this);?>
"><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete-5'), $this);?>
</a>
	</form>

</p>