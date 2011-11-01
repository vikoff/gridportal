<?php /* Smarty version 2.6.26, created on 2011-08-28 11:11:32
         compiled from Task/xrsl_edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'lng', 'Task/xrsl_edit.tpl', 28, false),array('function', 'a', 'Task/xrsl_edit.tpl', 71, false),)), $this); ?>
<script type="text/javascript" language="javascript"><?php echo '

$(document).ready(function() {

jQuery.fn.center = function () {
    this.css("position","fixed");
    this.css("top", ( $(window).height() - this.height()) / 2 + "px");
    this.css("left", ( $(window).width() - this.width()) / 2 + "px");
    return this;
}


    $(window).resize(function() {
        $(".popup-container").css({width: $(this).width() + \'px\', height: $(this).height() + \'px\'});
    });

    $("input[type=submit]").click(function() {
	$(window).resize();
	$(".popup").center();
	$(".popup .body-container").center();
	$("#wait-popup").show();
    });


});
'; ?>
</script>

<h2><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.taskset'), $this);?>
</h2>

<form action="" method="post">
	<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

	<input type="hidden" name="id" value="<?php echo (isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : ''); ?>
" />
	
	<table style="text-align: left; margin: auto;">
	<tr>
		<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.server'), $this);?>
</td>
		<td>
			<select name="server">
				<option value="thei.org.ua:7512">thei.org.ua </option>
			    <option value="grid.org.ua:7512">grid.org.ua </option> 
            </select>
		</td>
	</tr>
	<tr>
		<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.username'), $this);?>
</td>
		<td><input type="text" name="user[name]" value="" /></td>
	</tr>
	<tr>
		<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.password'), $this);?>
</td>
		<td><input type="password" name="user[pass]" value="" /></td>
	</tr>
	<tr>

	<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.max-time'), $this);?>
</td>
		<td><select name="lifetime"><option value="2850">2850</option><option value="1200">1200</option></select></td>
	</tr>
	
	</table>
	
	<br /><br /><br />
	<table style="text-align: left; margin: auto;">
	<?php $_from = (isset($this->_tpl_vars['gridjobfile']) ? $this->_tpl_vars['gridjobfile'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row']):
?>
		<?php if ((isset($this->_tpl_vars['row']['type']) ? $this->_tpl_vars['row']['type'] : '') == 'config'): ?>
			<tr><td><?php echo (isset($this->_tpl_vars['row']['title']) ? $this->_tpl_vars['row']['title'] : ''); ?>
</td><td><input type="text" name="xrsl[<?php echo (isset($this->_tpl_vars['row']['name']) ? $this->_tpl_vars['row']['name'] : ''); ?>
]" value="<?php echo (isset($this->_tpl_vars['row']['value_escaped']) ? $this->_tpl_vars['row']['value_escaped'] : ''); ?>
" style="width: 400px;" /></td></tr>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
	</table>
	
	<div class="paragraph">
		<input class="button" type="submit" name="action[task/run]" value="Запуск" />
		<?php echo SmartyPlugins::function_a(array('href' => 'task','class' => 'button','text' => 'Вернуться к списку задач'), $this);?>

	</div>
    
    <div id="wait-popup" class="popup-container" style="display:none">
	<div class="popup">
		<div class="body-container">		
			<img src="images/load.gif" alt="Загрузка" />
			<p><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.starting-task'), $this);?>
</p>
		</div>
	</div>
</div>
</form>