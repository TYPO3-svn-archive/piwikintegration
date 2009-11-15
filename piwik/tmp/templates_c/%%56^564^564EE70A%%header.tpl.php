<?php /* Smarty version 2.6.25, created on 2009-07-04 10:26:07
         compiled from CoreUpdater/templates/header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'translate', 'CoreUpdater/templates/header.tpl', 1, false),)), $this); ?>
<?php $this->assign('HTML_TITLE', ((is_array($_tmp='CoreUpdater_UpdateTitle')) ? $this->_run_mod_handler('translate', true, $_tmp) : smarty_modifier_translate($_tmp))); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/simple_structure_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<link rel="shortcut icon" href="plugins/CoreHome/templates/images/favicon.ico"> 