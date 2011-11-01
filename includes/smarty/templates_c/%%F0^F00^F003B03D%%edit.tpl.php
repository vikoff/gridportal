<?php /* Smarty version 2.6.26, created on 2011-09-24 00:18:39
         compiled from Software/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Software/edit.tpl', 26, false),)), $this); ?>

<ul id="submit-box-floating"></ul>

<h2><?php echo (isset($this->_tpl_vars['pageTitle']) ? $this->_tpl_vars['pageTitle'] : ''); ?>
</h2>

<form id="edit-form" action="" method="post">
	<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

	<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
" />

	<div class="paragraph">
		<label class="title">Название</label>
		<input type="text" name="name" value="<?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
" />
	</div>
	<div class="paragraph">
		<label class="title">Проект</label>
		<select name="project_id">
			<option value="">Выберите...</option>
			<?php $_from = (isset($this->_tpl_vars['projectsList']) ? $this->_tpl_vars['projectsList'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p']):
?>
				<option value="<?php echo (isset($this->_tpl_vars['p']['id']) ? $this->_tpl_vars['p']['id'] : ''); ?>
"><?php echo (isset($this->_tpl_vars['p']['name']) ? $this->_tpl_vars['p']['name'] : ''); ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
	</div>
	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[software/save][admin/content/software/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[software/save][admin/content/software/edit/<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?><?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
<?php else: ?>(%id%)<?php endif; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/software/list"), $this);?>
" title="Отменить все изменения и вернуться к списку">отмена</a>
		<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
		<a id="submit-delete" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/software/delete/".((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''))), $this);?>
" title="Удалить запись">удалить</a>
		<?php endif; ?>
		<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
		<a id="submit-copy" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/software/copy/".((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''))), $this);?>
" title="Сделать копию записи">копировать</a>
		<?php endif; ?>
	</div>
</form>

<script type="text/javascript"><?php echo '

$(function(){
	$("#edit-form").validate( { '; ?>
<?php echo (isset($this->_tpl_vars['validation']) ? $this->_tpl_vars['validation'] : ''); ?>
<?php echo ' } );
	enableFloatingSubmits();
});

'; ?>
</script>