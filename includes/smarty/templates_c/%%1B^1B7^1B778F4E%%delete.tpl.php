<?php /* Smarty version 2.6.26, created on 2011-11-10 21:05:54
         compiled from User/delete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'User/delete.tpl', 7, false),)), $this); ?>

<p>
	Вы уверены, что хотите удалить пользователя <?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
 <?php echo (isset($this->_tpl_vars['surname']) ? $this->_tpl_vars['surname'] : ''); ?>

</p>

<p>
<form action='<?php echo SmartyPlugins::function_a(array('href' => "admin/users/list"), $this);?>
' method="post">
	<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />
	<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

	
	<input class="button" type="submit" name="action[user/delete]" value="Удалить безвозвратно" />
	<input class="button" type="submit" name="cancel" value="Отмена" />
</form>
</p>