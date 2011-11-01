<?php /* Smarty version 2.6.26, created on 2011-09-27 21:43:43
         compiled from Task/stop.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Task/stop.php', 15, false),)), $this); ?>

<p>
Остановить задачу <b><?php echo '<?='; ?>
 $this->name; <?php echo '?>'; ?>
</b>?
</p>

<form action="" method="post">
	<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />
	<?php echo '<?='; ?>
 FORMCODE <?php echo '?>'; ?>

	
	<input class="button" type="submit" name="action[task/stop]" value="Остановить" />
	<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "task/list"), $this);?>
">Отмена</a>
</form>