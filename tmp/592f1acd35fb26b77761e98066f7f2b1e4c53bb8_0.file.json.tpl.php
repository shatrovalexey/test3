<?php
/* Smarty version 3.1.31, created on 2017-08-03 23:01:03
  from "/mnt/sdb1/www/test2/view/json.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_598380ff492875_55905781',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '592f1acd35fb26b77761e98066f7f2b1e4c53bb8' => 
    array (
      0 => '/mnt/sdb1/www/test2/view/json.tpl',
      1 => 1501790434,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_598380ff492875_55905781 (Smarty_Internal_Template $_smarty_tpl) {
?>
window.$<?php echo $_smarty_tpl->tpl_vars['result']->value['key'];?>
 = <?php echo json_encode($_smarty_tpl->tpl_vars['result']->value['data']);?>
 ;<?php }
}
