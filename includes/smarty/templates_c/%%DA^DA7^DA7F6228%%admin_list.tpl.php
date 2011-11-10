<?php /* Smarty version 2.6.26, created on 2011-11-10 20:50:34
         compiled from User/admin_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'User/admin_list.tpl', 26, false),)), $this); ?>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>


<?php if ((isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : '')): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?php echo (isset($this->_tpl_vars['sorters']['id']) ? $this->_tpl_vars['sorters']['id'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['email']) ? $this->_tpl_vars['sorters']['email'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['surname']) ? $this->_tpl_vars['sorters']['surname'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['dn']) ? $this->_tpl_vars['sorters']['dn'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['dn_cn']) ? $this->_tpl_vars['sorters']['dn_cn'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['level']) ? $this->_tpl_vars['sorters']['level'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['regdate']) ? $this->_tpl_vars['sorters']['regdate'] : ''); ?>
</th>
		<th>Опции</th>
	</tr>
	<?php $_from = (isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
	<tr>
		<td><?php echo (isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['email']) ? $this->_tpl_vars['item']['email'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['fio']) ? $this->_tpl_vars['item']['fio'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['dn']) ? $this->_tpl_vars['item']['dn'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['dn_cn']) ? $this->_tpl_vars['item']['dn_cn'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['level']) ? $this->_tpl_vars['item']['level'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['regdate']) ? $this->_tpl_vars['item']['regdate'] : ''); ?>
</td>
		<td style="font-size: 11px;">
			<?php echo SmartyPlugins::function_a(array('href' => "admin/users/view/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : '')),'text' => "Подробней"), $this);?>

		</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
<?php else: ?>
	<p>Сохраненных записей пока нет.</p>
<?php endif; ?>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>

