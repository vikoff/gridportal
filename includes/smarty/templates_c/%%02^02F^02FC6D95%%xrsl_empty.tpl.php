<?php /* Smarty version 2.6.26, created on 2011-10-11 23:00:40
         compiled from Task/xrsl_empty.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'lng', 'Task/xrsl_empty.tpl', 2, false),array('function', 'a', 'Task/xrsl_empty.tpl', 2, false),)), $this); ?>

<p><b>nordujob</b><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrsl-empty-notfound'), $this);?>
<a href="<?php echo SmartyPlugins::function_a(array('href' => "task/upload-files/".((isset($this->_tpl_vars['id']) ? $this->_tpl_vars['id'] : ''))), $this);?>
" class="button"><?php echo SmartyPlugins::function_lng(array('snippet' => 'xrsl-empty-download'), $this);?>
</a></p>