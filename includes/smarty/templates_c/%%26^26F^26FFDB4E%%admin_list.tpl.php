<?php /* Smarty version 2.6.26, created on 2011-09-24 00:19:28
         compiled from UserStatistics/admin_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'UserStatistics/admin_list.tpl', 3, false),array('modifier', 'default', 'UserStatistics/admin_list.tpl', 40, false),)), $this); ?>
<p>
	<div style="float: right;">
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "admin/root/user-statistics/delete"), $this);?>
">Очистить статистику</a>
	</div>
	<div class="clear"> </div>
</p>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>


<?php if ((isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : '')): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?php echo (isset($this->_tpl_vars['sorters']['id']) ? $this->_tpl_vars['sorters']['id'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['uid']) ? $this->_tpl_vars['sorters']['uid'] : ''); ?>
</th>
		<th>Посещенные страницы</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['user_ip']) ? $this->_tpl_vars['sorters']['user_ip'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['referer']) ? $this->_tpl_vars['sorters']['referer'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['has_js']) ? $this->_tpl_vars['sorters']['has_js'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['browser']) ? $this->_tpl_vars['sorters']['browser'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['screen_resolution']) ? $this->_tpl_vars['sorters']['screen_resolution'] : ''); ?>
</th>
		<th>Опции</th>
	</tr>
	<?php $_from = (isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
	<tr>
		<td><?php echo (isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['user']) ? $this->_tpl_vars['item']['user'] : ''); ?>
</td>
		<td>
			<?php if ((isset($this->_tpl_vars['item']['has_pages']) ? $this->_tpl_vars['item']['has_pages'] : '')): ?>
				Всего: <?php echo (isset($this->_tpl_vars['item']['num_pages']) ? $this->_tpl_vars['item']['num_pages'] : ''); ?>
 страниц<br />
				<div style="color: #999;">
					<div style="margin-top: 2px;">Первая: <?php echo (isset($this->_tpl_vars['item']['first_page']['date']) ? $this->_tpl_vars['item']['first_page']['date'] : ''); ?>
</div>
					<div style="font-size: 10px;"><?php echo (isset($this->_tpl_vars['item']['first_page']['url']) ? $this->_tpl_vars['item']['first_page']['url'] : ''); ?>
</div>
					<div style="margin-top: 2px;">Последняя: <?php echo (isset($this->_tpl_vars['item']['last_page']['date']) ? $this->_tpl_vars['item']['last_page']['date'] : ''); ?>
</div>
					<div style="font-size: 10px;"><?php echo (isset($this->_tpl_vars['item']['last_page']['url']) ? $this->_tpl_vars['item']['last_page']['url'] : ''); ?>
</div>
				</div>
			<?php else: ?>
				-
			<?php endif; ?>
		</td>
		<td><?php echo ((is_array($_tmp=(isset($this->_tpl_vars['item']['user_ip']) ? $this->_tpl_vars['item']['user_ip'] : ''))) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</td>
		<td><?php echo ((is_array($_tmp=(isset($this->_tpl_vars['item']['referer']) ? $this->_tpl_vars['item']['referer'] : ''))) ? $this->_run_mod_handler('default', true, $_tmp, '-') : smarty_modifier_default($_tmp, '-')); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['has_js_text']) ? $this->_tpl_vars['item']['has_js_text'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['browser']) ? $this->_tpl_vars['item']['browser'] : ''); ?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['screen_resolution']) ? $this->_tpl_vars['item']['screen_resolution'] : ''); ?>
</td>
		<td style="font-size: 11px;">
			<?php echo SmartyPlugins::function_a(array('href' => "admin/root/user-statistics/view/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : '')),'text' => "Подробней"), $this);?>
<br />
		</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
<?php else: ?>
	<p>Сохраненных записей пока нет.</p>
<?php endif; ?>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>

