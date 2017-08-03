<?php
/* Smarty version 3.1.31, created on 2017-08-03 23:10:23
  from "/mnt/sdb1/www/test2/view/default.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5983832f3f79e8_41627410',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '67ce9c0456e24a403c1a14c9af0a744745b59f91' => 
    array (
      0 => '/mnt/sdb1/www/test2/view/default.tpl',
      1 => 1501791014,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5983832f3f79e8_41627410 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['meta']['title']);?>
</title>
		<meta charset="<?php echo $_smarty_tpl->tpl_vars['config']->value['charset'];?>
">
		<link rel="stylesheet" href="/css/style.css" type="text/css">
		<?php echo '<script'; ?>
 src="http://code.jquery.com/jquery-3.2.1.min.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/message"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="/js/script.js"><?php echo '</script'; ?>
>
	</head>
	<body>
		<div class="page-overlay">
			<nav class="page-navigation">
				<ul>
					<li>
						<a href="/position">Должности</a>
					</li>
					<li>
						<a href="/employer">Работники</a>
					</li>
				</ul>
				<div class="both"></div>
			</nav>


			<div class="page-header">
				<h3><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['meta']['title']);?>
</h3>
			</div>
			<div class="page-body">
				<h1 class="page-body-overlay"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['meta']['title']);?>
</h1>
				<div class="page-body-body"><?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['include']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
</div>
			</div>
			<footer>
				<h3><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['meta']['title']);?>
</h3>
				<p>Автор: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value['author']);?>

			</footer>
		</div>
	</body>
</html><?php }
}
