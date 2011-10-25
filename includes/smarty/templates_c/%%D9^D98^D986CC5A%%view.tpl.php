<?php /* Smarty version 2.6.26, created on 2011-09-24 20:19:38
         compiled from User/view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'User/view.tpl', 2, false),)), $this); ?>

<div><?php echo SmartyPlugins::function_a(array('href' => "admin/users/list",'text' => "Вернуться к списку"), $this);?>
</div>

<h2>
	Пользователь #<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>

</h2>

<table class="std-grid">
<tr>
	<td class="title">email</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['email']) ? $this->_tpl_vars['email'] : ''); ?>
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
	<td class="title">Отчество</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['patronymic']) ? $this->_tpl_vars['patronymic'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">Пол</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['sex']) ? $this->_tpl_vars['sex'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">Дата рождения</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['birthdate']) ? $this->_tpl_vars['birthdate'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">Страна</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['country']) ? $this->_tpl_vars['country'] : ''); ?>
</td>
</tr>
<tr>
	<td class="title">Город</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['city']) ? $this->_tpl_vars['city'] : ''); ?>
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
	<td class="title">Активация</td>
	<td class="data"><?php echo (isset($this->_tpl_vars['active']) ? $this->_tpl_vars['active'] : ''); ?>
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