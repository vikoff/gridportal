<?php /* Smarty version 2.6.26, created on 2011-09-24 20:19:46
         compiled from User/delete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'User/delete.tpl', 54, false),)), $this); ?>

<p>
	Вы уверены, что хотите удалить пользователя #<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
 безвозвратно?
</p>

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
	<td class="data"><?php echo (isset($this->_tpl_vars['level']) ? $this->_tpl_vars['level'] : ''); ?>
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
</table>

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