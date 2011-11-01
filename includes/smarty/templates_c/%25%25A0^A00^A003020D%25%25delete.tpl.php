<?php /* Smarty version 2.6.26, created on 2011-09-03 22:19:11
         compiled from Task/delete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Task/delete.tpl', 10, false),)), $this); ?>
<p>

	Хотите удалить задачу <b><?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
</b> <?php if ((isset($this->_tpl_vars['is_test']) ? $this->_tpl_vars['is_test'] : '')): ?>(тестовая)<?php else: ?>со всеми файлами<?php endif; ?>?

	<form action="" method="post">
		<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />
		<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

		
		<input class="button" type="submit" name="action[task/delete]" value="Удалить" />
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "task/list"), $this);?>
">Отмена</a>
	</form>

</p>