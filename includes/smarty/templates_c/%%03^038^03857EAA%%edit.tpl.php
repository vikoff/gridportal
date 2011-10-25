<?php /* Smarty version 2.6.26, created on 2011-09-23 23:08:30
         compiled from Project/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Project/edit.tpl', 28, false),)), $this); ?>

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
		<fieldset title="Виртуальные организации">
			<legend style="font-weight: bold;">Виртуальные организации</legend>
			<?php $_from = (isset($this->_tpl_vars['vomsList']) ? $this->_tpl_vars['vomsList'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
				<input id="checkbox-voms-<?php echo (isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : ''); ?>
" type="checkbox" name="voms[]" value="<?php echo (isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : ''); ?>
" <?php if ((isset($this->_tpl_vars['allowedVoms'][(isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : '')]) ? $this->_tpl_vars['allowedVoms'][(isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : '')] : '')): ?>checked="checked"<?php endif; ?>>
				<label for="checkbox-voms-<?php echo (isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : ''); ?>
"><?php echo (isset($this->_tpl_vars['v']['name']) ? $this->_tpl_vars['v']['name'] : ''); ?>
</label><br />
			<?php endforeach; endif; unset($_from); ?>
		</fieldset>
	</div>
	
	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[project/save][admin/content/project/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[project/save][admin/content/project/edit/<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?><?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
<?php else: ?>(%id%)<?php endif; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/project/list"), $this);?>
" title="Отменить все изменения и вернуться к списку">отмена</a>
		<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
		<a id="submit-delete" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/project/delete/".((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''))), $this);?>
" title="Удалить запись">удалить</a>
		<?php endif; ?>
		<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
		<a id="submit-copy" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/project/copy/".((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''))), $this);?>
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