<?php /* Smarty version 2.6.26, created on 2011-09-22 17:56:13
         compiled from Page/admin_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Page/admin_list.tpl', 3, false),)), $this); ?>

<div class="options-row">
	<?php echo SmartyPlugins::function_a(array('href' => "admin/content/page/new",'text' => "Добавить запись"), $this);?>

</div>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>


<?php if ((isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : '')): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?php echo (isset($this->_tpl_vars['sorters']['id']) ? $this->_tpl_vars['sorters']['id'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['title']) ? $this->_tpl_vars['sorters']['title'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['type']) ? $this->_tpl_vars['sorters']['type'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['alias']) ? $this->_tpl_vars['sorters']['alias'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['modif_date']) ? $this->_tpl_vars['sorters']['modif_date'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['published']) ? $this->_tpl_vars['sorters']['published'] : ''); ?>
</th>
		<th>Опции</th>
	</tr>
	<?php $_from = (isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
	<tr <?php if (! (isset($this->_tpl_vars['item']['published']) ? $this->_tpl_vars['item']['published'] : '')): ?>class="unpublished"<?php endif; ?>>
		<td><?php echo (isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''); ?>
</td>
		<td><?php echo SmartyPlugins::function_a(array('href' => "admin/content/page/edit/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : '')),'text' => (isset($this->_tpl_vars['item']['title']) ? $this->_tpl_vars['item']['title'] : '')), $this);?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['type_str']) ? $this->_tpl_vars['item']['type_str'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['alias']) ? $this->_tpl_vars['item']['alias'] : ''); ?>
</td>
		<td class="center"><?php echo (isset($this->_tpl_vars['item']['modif_date']) ? $this->_tpl_vars['item']['modif_date'] : ''); ?>
</td>
		<td class="center" style="width: 140px;">
		
			<div class="tr-hover-opened" style="height: 18px;">
				<form class="inline" action="" method="post">
					<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''); ?>
" />
					<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

					<?php if ((isset($this->_tpl_vars['item']['published']) ? $this->_tpl_vars['item']['published'] : '')): ?>
						<input class="button-small" type="submit" name="action[page/unpublish]" value="Скрыть" />
					<?php else: ?>
						<input class="button-small" type="submit" name="action[page/publish]" value="Опубликовать" />
					<?php endif; ?>
				</form>
			</div>
			
			<div class="tr-hover-closed" style="height: 18px;">
				<?php if ((isset($this->_tpl_vars['item']['published']) ? $this->_tpl_vars['item']['published'] : '')): ?>Опубл.<?php else: ?>Скрыт<?php endif; ?>
			</div>
		</td>
		<td class="options">
			<div class="tr-hover-visible">
				<a href="<?php echo SmartyPlugins::function_a(array('href' => "page/".((isset($this->_tpl_vars['item']['alias']) ? $this->_tpl_vars['item']['alias'] : ''))), $this);?>
" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/page/edit/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?php echo SmartyPlugins::function_a(array('href' => "admin/content/page/delete/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
<?php else: ?>
	<p>Сохраненных записей пока нет.</p>
<?php endif; ?>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>

