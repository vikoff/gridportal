<?php /* Smarty version 2.6.26, created on 2011-10-01 17:19:37
         compiled from Task/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'a', 'Task/list.tpl', 3, false),array('function', 'lng', 'Task/list.tpl', 3, false),)), $this); ?>

<div align="right" style="margin-right: 50px;">
	<a href="<?php echo SmartyPlugins::function_a(array('href' => "task/new"), $this);?>
"><?php echo SmartyPlugins::function_lng(array('snippet' => 'tasklist.add-new'), $this);?>
</a>
</div>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>


<?php if ((isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : '')): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?php echo (isset($this->_tpl_vars['sorters']['name']) ? $this->_tpl_vars['sorters']['name'] : ''); ?>
</th>
		<th><?php echo SmartyPlugins::function_lng(array('snippet' => 'tasklist.xrsl-presence'), $this);?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['state_title']) ? $this->_tpl_vars['sorters']['state_title'] : ''); ?>
</th>
		<th><?php echo (isset($this->_tpl_vars['sorters']['date']) ? $this->_tpl_vars['sorters']['date'] : ''); ?>
</th>
		<th colspan="10"><?php echo SmartyPlugins::function_lng(array('snippet' => 'options'), $this);?>
</th>
	</tr>
	<?php $_from = (isset($this->_tpl_vars['collection']) ? $this->_tpl_vars['collection'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
	<tr>
		<td><?php echo (isset($this->_tpl_vars['item']['name']) ? $this->_tpl_vars['item']['name'] : ''); ?>
 <?php if ((isset($this->_tpl_vars['item']['is_test']) ? $this->_tpl_vars['item']['is_test'] : '')): ?><sup><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete-2'), $this);?>
</sup><?php endif; ?></td>
		<td><?php if ((isset($this->_tpl_vars['item']['is_gridjob_loaded']) ? $this->_tpl_vars['item']['is_gridjob_loaded'] : '')): ?><span class="green"><?php echo SmartyPlugins::function_lng(array('snippet' => 'TaskList.present'), $this);?>
</span><?php else: ?><span class="red"><?php echo SmartyPlugins::function_lng(array('snippet' => 'TaskList.no'), $this);?>
</span><?php endif; ?></td>
		<td><?php echo SmartyPlugins::function_lng(array('snippet' => (isset($this->_tpl_vars['item']['state_title']) ? $this->_tpl_vars['item']['state_title'] : '')), $this);?>
</td>
		<td><?php echo (isset($this->_tpl_vars['item']['date_str']) ? $this->_tpl_vars['item']['date_str'] : ''); ?>
</td>
		
			<td class="option"><?php if ((isset($this->_tpl_vars['item']['actions']['rename']) ? $this->_tpl_vars['item']['actions']['rename'] : '')): ?><a href="<?php echo SmartyPlugins::function_a(array('href' => "task/edit/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class='button-small'><?php echo SmartyPlugins::function_lng(array('snippet' => 'rename'), $this);?>
</a><?php endif; ?></td>
			<td class="option"><?php if ((isset($this->_tpl_vars['item']['actions']['file_manager']) ? $this->_tpl_vars['item']['actions']['file_manager'] : '')): ?><a href="<?php echo SmartyPlugins::function_a(array('href' => "task/upload-files/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class='button-small'><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.files'), $this);?>
</a><?php endif; ?></td>
			<td class="option"><?php if ((isset($this->_tpl_vars['item']['actions']['run']) ? $this->_tpl_vars['item']['actions']['run'] : '')): ?><a href="<?php echo SmartyPlugins::function_a(array('href' => "task/xrsl-edit/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class='button-small'><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.run'), $this);?>
</a><?php endif; ?></td>
			<td class="option"><?php if ((isset($this->_tpl_vars['item']['actions']['stop']) ? $this->_tpl_vars['item']['actions']['stop'] : '')): ?><a href="<?php echo SmartyPlugins::function_a(array('href' => "task/stop/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class='button-small'><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.stop'), $this);?>
</a><?php endif; ?></td>
			<td class="option"><?php if ((isset($this->_tpl_vars['item']['actions']['get_results']) ? $this->_tpl_vars['item']['actions']['get_results'] : '')): ?><a href="<?php echo SmartyPlugins::function_a(array('href' => "task/get-results/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class='button-small'><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.get-result'), $this);?>
</a><?php endif; ?></td>
			<td class="option"><?php if ((isset($this->_tpl_vars['item']['actions']['delete']) ? $this->_tpl_vars['item']['actions']['delete'] : '')): ?><a href="<?php echo SmartyPlugins::function_a(array('href' => "task/delete/".((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''))), $this);?>
" class='button-small'><?php echo SmartyPlugins::function_lng(array('snippet' => 'task.delete'), $this);?>
</a><?php endif; ?></td>
		</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
<?php else: ?>
	<p><?php echo SmartyPlugins::function_lng(array('snippet' => 'tasklist.no-task'), $this);?>
</p>
<?php endif; ?>

<?php echo (isset($this->_tpl_vars['pagination']) ? $this->_tpl_vars['pagination'] : ''); ?>
