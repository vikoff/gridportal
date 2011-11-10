<?php /* Smarty version 2.6.26, created on 2011-11-10 20:59:26
         compiled from User/view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'User/view.tpl', 2, false),)), $this); ?>

<div><?php echo SmartyPlugins::function_a(array('href' => "admin/users/list",'text' => "Вернуться к списку"), $this);?>
</div>

<h2>
	Пользователь <?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
 <?php echo (isset($this->_tpl_vars['surname']) ? $this->_tpl_vars['surname'] : ''); ?>

</h2>

<table class="std-grid">
<tr>
	<td class="title">email</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['profile']['email']) ? $this->_tpl_vars['profile']['email'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">Фамилия</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['surname']) ? $this->_tpl_vars['surname'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">Имя</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">dn</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['dn']) ? $this->_tpl_vars['dn'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">dn_cn</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['dn_cn']) ? $this->_tpl_vars['dn_cn'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">Права</td>
	<td class="data">
	<?php if ((isset($this->_tpl_vars['perms']['allowEdit']) ? $this->_tpl_vars['perms']['allowEdit'] : '')): ?>
		<form class="inline" action="" method="post">
			<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

			<input type="hidden" name="instance-id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />
			<select name="level"><?php echo (isset($this->_tpl_vars['perms']['list']) ? $this->_tpl_vars['perms']['list'] : ''); ?>
</select>
			<input type="submit" name="action[user/save-perms]" value="Сохранить" />
		</form>
	<?php else: ?>
		<?php echo (isset($this->_tpl_vars['perms']['curTitle']) ? $this->_tpl_vars['perms']['curTitle'] : ''); ?>

	<?php endif; ?>
	</td>
</tr>
<tr>
	<td class="title">Дата регистрации</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['regdate']) ? $this->_tpl_vars['regdate'] : ''); ?>
</td>
</tr>
<tr>
	<td>Действия</td>
	<td class="actions">
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/users/list"), $this);?>
">Вернуться к списку</a>
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/users/delete/".((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''))), $this);?>
">Удалить пользователя</a>
	</td>
</tr>
</table>