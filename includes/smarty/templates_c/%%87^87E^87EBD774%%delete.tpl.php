<?php /* Smarty version 2.6.26, created on 2011-09-17 17:47:24
         compiled from Voms/delete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Voms/delete.tpl', 16, false),)), $this); ?>

<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить ВО <b><?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
</b> безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />
			<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

			
			<input class="button" type="submit" name="action[voms/delete]" value="Удалить" />
			<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/voms/list"), $this);?>
">Отмена</a>
		</form>
	</div>
	
</div>