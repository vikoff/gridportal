<?php /* Smarty version 2.6.26, created on 2011-11-18 22:18:16
         compiled from TaskSet/submit.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'lng', 'TaskSet/submit.php', 28, false),array('function', 'a', 'TaskSet/submit.php', 87, false),)), $this); ?>
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
	
	<table style="text-align: left; margin: 1em auto;">
	<?php if ((isset($this->_tpl_vars['showMyproxyLogin']) ? $this->_tpl_vars['showMyproxyLogin'] : '')): ?>
		<tr>
			<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.server'), $this);?>
</td>
			<td>
				<select name="server">
					<?php $_from = (isset($this->_tpl_vars['myproxyServersList']) ? $this->_tpl_vars['myproxyServersList'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="<?php echo (isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''); ?>
"><?php echo (isset($this->_tpl_vars['item']['name']) ? $this->_tpl_vars['item']['name'] : ''); ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
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
	<?php else: ?>
		<tr style="display: none;"><td><input type="hidden" name="myproxy-autologin" value="1" /></td></tr>
	<?php endif; ?>
	</table>
	
	<table style="margin: 1em auto;">
	<tr>
		<td>Предпочитаемый сервер<br />(оставить пустым, если не надо)</td>
		<td><input type="text" name="prefer-server" value="" /></td>
	</tr>
	</table>
	
	<div class="paragraph">
		<?php echo '<?'; ?>
 if ($numSubmits > 1): <?php echo '?>'; ?>

			Будет запущено <?php echo '<?='; ?>
 $numSubmits; <?php echo '?>'; ?>
 задач.
		<?php echo '<?'; ?>
 else: <?php echo '?>'; ?>

			Будет запущена одна задача.
		<?php echo '<?'; ?>
 endif; <?php echo '?>'; ?>

	</div>
	<div class="paragraph">
		<input class="button" type="submit" name="action[task-set/submit]" value="<?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.start-task'), $this);?>
" />
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "task-set/customize/".((isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : ''))), $this);?>
"><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.manage-files'), $this);?>
</a>
		<a class="button" href="<?php echo SmartyPlugins::function_a(array('href' => "task-set/list"), $this);?>
"><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.go-to-task-list'), $this);?>
</a>
	</div>
    
    <div id="wait-popup" class="popup-container" style="display:none">
	<div class="popup">
		<div class="body-container">		
			<img src="images/load.gif" alt="<?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.starting-task'), $this);?>
" />
			<p><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrls_edit.starting-task'), $this);?>
</p>
		</div>
	</div>
</div>
</form>