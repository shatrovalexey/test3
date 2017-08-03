<?php
/* Smarty version 3.1.31, created on 2017-08-03 23:22:20
  from "/mnt/sdb1/www/test2/view/results-table.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_598385fcecba85_80034198',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '95544168e9a5de9eeb0e47a060557f5b43110980' => 
    array (
      0 => '/mnt/sdb1/www/test2/view/results-table.tpl',
      1 => 1501791731,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_598385fcecba85_80034198 (Smarty_Internal_Template $_smarty_tpl) {
?>

<h2><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['data']['rows']['table']['comment']);?>
</h2>

<form method="post" class="table-results-form-create">
	<div class="table-results-form-fields">
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['result']->value['data']['rows']['table']['header'], 'header');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['header']->value) {
?>
		<?php if ($_smarty_tpl->tpl_vars['header']->value['key'] == 'pri') {?>
		<input type="hidden" name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['name']);?>
" value="">
		<?php } elseif ($_smarty_tpl->tpl_vars['header']->value['key'] == 'foreign') {?>
		<label>
			<span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['comment']);?>
</span>:
			<select name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['name']);?>
" required="required">
				<option value="">значение</option>
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['result']->value['data'][$_smarty_tpl->tpl_vars['header']->value['reference']]['result'], 'reference');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['reference']->value) {
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['reference']->value['id'];?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['reference']->value['title']);?>
</option>
				<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

			</select>
			<div class="both"></div>
		</label>
		<?php } else { ?>
		<label>
			<span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['comment']);?>
</span>:
			<?php if ($_smarty_tpl->tpl_vars['header']->value['type'] == 'number') {?>
			<input placeholder="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['comment']);?>
" type="number" name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['name']);?>
" value="" required="required">
			<?php } elseif ($_smarty_tpl->tpl_vars['header']->value['type'] == 'text') {?>
			<textarea placeholder="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['comment']);?>
" name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['name']);?>
"></textarea>
			<?php } else { ?>
			<input placeholder="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['comment']);?>
" type="text" name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['name']);?>
" value="" required="required">
			<?php }?>
			<div class="both"></div>
		</label>
		<?php }?>
	<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

	</div>
	<div class="table-results-form-footer">
		<label>
			<span>действие</span>
			<select name="action">
				<option value="create">создать</option>
				<option value="update">редактировать</option>
			</select>
		</label>
		<label>
			<span>выполнить</span>
			<input type="submit" value="&rarr;">
		</label>
	</div>
</form>
<?php if ($_smarty_tpl->tpl_vars['result']->value['data']['rows']['result']) {?>
<div class="table-results-form table-results-form-<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['data']['rows']['table']['name']);?>
">
	<p>Выберите запись чтобы её редактировать.
	<table class="table-results">
		<thead>
			<tr>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['result']->value['data']['rows']['table']['header'], 'header', false, 'i');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['header']->value) {
?>
				<th class="table-results-col table-results-col-<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['name']);?>
">
					<a href="?page=<?php echo $_smarty_tpl->tpl_vars['result']->value['data']['rows']['page_current'];?>
&amp;order_col=<?php echo $_smarty_tpl->tpl_vars['i']->value+1;?>
&amp;order_arr=<?php if ($_smarty_tpl->tpl_vars['result']->value['data']['rows']['order_col'] == $_smarty_tpl->tpl_vars['i']->value+1) {
echo ($_smarty_tpl->tpl_vars['result']->value['data']['rows']['order_arr']);
} else { ?>1<?php }?>"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['comment']);?>
</a>
					<span><?php if ($_smarty_tpl->tpl_vars['result']->value['data']['rows']['order_col'] == $_smarty_tpl->tpl_vars['i']->value+1) {
if ($_smarty_tpl->tpl_vars['result']->value['data']['rows']['order_arr'] > 0) {?>&uarr;<?php } else { ?>&darr;<?php }
}?></span>
				</th>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

				<th class="table-results-col table-results-col-_actions">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['result']->value['data']['rows']['result'], 'row');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
?>
			<tr class="table-results-row" data-action="update">
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['result']->value['data']['rows']['table']['header'], 'header');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['header']->value) {
?>
				<td
					data-name="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['name']);?>
"
					data-value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['header']->value['name']]);?>
"
					data-key="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['key']);?>
"
					class="table-results-cell table-results-cell-<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value['name']);?>
"
				>
					<?php if ($_smarty_tpl->tpl_vars['header']->value['key'] == "pri") {?>
						<span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['header']->value['name']]);?>
</span>
					<?php } elseif ($_smarty_tpl->tpl_vars['header']->value['key'] == "foreign") {?>
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['result']->value['data'][$_smarty_tpl->tpl_vars['header']->value['reference']]['result'], 'reference');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['reference']->value) {
?>
							<?php if ($_smarty_tpl->tpl_vars['reference']->value['id'] != $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['header']->value['name']]) {?>
								<?php
continue 1;?>
							<?php }?>

							<span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['reference']->value['title']);?>
</span>
							<?php
break 1;?>
						<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

					<?php } else { ?>
						<span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['header']->value['name']]);?>
</span>
					<?php }?>
					</div>
				</td>
				<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

				<td class="table-results-cell">
					<a href="?id=<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
&amp;action=delete" title="удалить" class="table-results-delete">X</a>
				</td>
			</tr>
		<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

		</tbody>
	</table>

	<?php if ($_smarty_tpl->tpl_vars['result']->value['data']['rows']['page_count'] > 1) {?>
	<div class="table-results-pager">
		<ul>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['result']->value['data']['rows']['pager'], 'page_no', false, 'page_i');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['page_i']->value => $_smarty_tpl->tpl_vars['page_no']->value) {
?>
			<li>
			<?php if ($_smarty_tpl->tpl_vars['page_i']->value == $_smarty_tpl->tpl_vars['result']->value['data']['rows']['page_current']) {?>
				<span class="table-results-pager-current"><?php echo $_smarty_tpl->tpl_vars['page_no']->value;?>
</span>
			<?php } else { ?>
				<a href="?page=<?php echo $_smarty_tpl->tpl_vars['page_i']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['page_no']->value;?>
</a>
			<?php }?>
			</li>
		<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

		</ul>
		<div class="both"></div>
	</div>
	<?php }?>
</div>
<?php }
}
}
