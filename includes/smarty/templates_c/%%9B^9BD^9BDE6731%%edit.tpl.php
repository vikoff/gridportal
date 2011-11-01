<?php /* Smarty version 2.6.26, created on 2011-09-24 00:18:59
         compiled from Voms/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Voms/edit.tpl', 21, false),)), $this); ?>

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
		<label class="title">url</label>
		<input type="text" name="url" value="<?php echo (isset($this->_tpl_vars['url']) ? $this->_tpl_vars['url'] : ''); ?>
" size="100" />
	</div>
	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[voms/save][admin/content/voms/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[voms/save][admin/content/voms/edit/<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?><?php echo (isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''); ?>
<?php else: ?>(%id%)<?php endif; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/voms/list"), $this);?>
" title="Отменить все изменения и вернуться к списку">отмена</a>
		<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
		<a id="submit-delete" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/voms/delete/".((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''))), $this);?>
" title="Удалить запись">удалить</a>
		<?php endif; ?>
		<?php if ((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : '')): ?>
		<a id="submit-copy" class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/voms/copy/".((isset($this->_tpl_vars['instanceId']) ? $this->_tpl_vars['instanceId'] : ''))), $this);?>
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