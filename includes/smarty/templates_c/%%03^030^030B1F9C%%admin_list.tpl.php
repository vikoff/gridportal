<?php /* Smarty version 2.6.26, created on 2011-09-23 19:45:37
         compiled from Voms/admin_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Voms/admin_list.tpl', 3, false),)), $this); ?>

<div class="options-row">
	<?php echo SmartyPlugins::function_a(array('href' => "admin/content/voms/new",'text' => "Добавить запись"), $this);?>

</div>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>


<?php if ((isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : '')): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?php echo (isset($this->_tpl_vars['sorters']['id']) ? $this->_tpl_vars['sorters']['id'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['name']) ? $this->_tpl_vars['sorters']['name'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['url']) ? $this->_tpl_vars['sorters']['url'] : ''); ?>
</th>
		<th>Опции</th>
	</tr>
	<?php $_from = (isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
	<tr>
		<td><?php echo (isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['name']) ? $this->_tpl_vars['item']['name'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['url']) ? $this->_tpl_vars['item']['url'] : ''); ?>
</td>
		<td class="center">
			<div class="tr-hover-visible options">
				<a href="<?php echo SmartyPlugins::function_a(array('href' => "voms/view/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class="item" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/voms/edit/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class="item" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/voms/delete/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class="item" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
<?php else: ?>
	<p>Сохраненных записей пока нет.</p>
<?php endif; ?>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>

