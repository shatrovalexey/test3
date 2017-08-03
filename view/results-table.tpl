{nocache}
<h2>{$result.data.rows.table.comment|htmlspecialchars}</h2>

<form method="post" class="table-results-form-create">
	<div class="table-results-form-fields">
	{foreach from=$result.data.rows.table.header item=$header}
		{if $header.key == 'pri'}
		<input type="hidden" name="{$header.name|htmlspecialchars}" value="">
		{elseif $header.key == 'foreign'}
		<label>
			<span>{$header.comment|htmlspecialchars}</span>:
			<select name="{$header.name|htmlspecialchars}" required="required">
				<option value="">значение</option>
				{foreach from=$result.data[$header.reference].result item=reference}
				<option value="{$reference.id}">{$reference.title|htmlspecialchars}</option>
				{/foreach}
			</select>
			<div class="both"></div>
		</label>
		{else}
		<label>
			<span>{$header.comment|htmlspecialchars}</span>:
			{if $header.type == 'number'}
			<input placeholder="{$header.comment|htmlspecialchars}" type="number" name="{$header.name|htmlspecialchars}" value="" required="required">
			{elseif $header.type == 'text'}
			<textarea placeholder="{$header.comment|htmlspecialchars}" name="{$header.name|htmlspecialchars}"></textarea>
			{else}
			<input placeholder="{$header.comment|htmlspecialchars}" type="text" name="{$header.name|htmlspecialchars}" value="" required="required">
			{/if}
			<div class="both"></div>
		</label>
		{/if}
	{/foreach}
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
{if $result.data.rows.result}
<div class="table-results-form table-results-form-{$result.data.rows.table.name|htmlspecialchars}">
	<p>Выберите запись чтобы её редактировать.
	<table class="table-results">
		<thead>
			<tr>
			{foreach from=$result.data.rows.table.header item=header key=i}
				<th class="table-results-col table-results-col-{$header.name|htmlspecialchars}">
					<a href="?page={$result.data.rows.page_current}&amp;order_col={$i+1}&amp;order_arr={if $result.data.rows.order_col==$i+1}{($result.data.rows.order_arr)}{else}1{/if}">{$header.comment|htmlspecialchars}</a>
					<span>{if $result.data.rows.order_col==$i+1}{if $result.data.rows.order_arr>0}&uarr;{else}&darr;{/if}{/if}</span>
				</th>
			{/foreach}
				<th class="table-results-col table-results-col-_actions">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$result.data.rows.result item=row}
			<tr class="table-results-row" data-action="update">
				{foreach from=$result.data.rows.table.header item=header}
				<td
					data-name="{$header.name|htmlspecialchars}"
					data-value="{$row[$header.name]|htmlspecialchars}"
					data-key="{$header.key|htmlspecialchars}"
					class="table-results-cell table-results-cell-{$header.name|htmlspecialchars}"
				>
					{if $header.key == "pri"}
						<span>{$row[$header.name]|htmlspecialchars}</span>
					{elseif $header.key == "foreign"}
						{foreach from=$result.data[$header.reference].result item=reference}
							{if $reference.id != $row[$header.name]}
								{continue}
							{/if}

							<span>{$reference.title|htmlspecialchars}</span>
							{break}
						{/foreach}
					{else}
						<span>{$row[$header.name]|htmlspecialchars}</span>
					{/if}
					</div>
				</td>
				{/foreach}
				<td class="table-results-cell">
					<a href="?id={$row.id}&amp;action=delete" title="удалить" class="table-results-delete">X</a>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	{if $result.data.rows.page_count > 1}
	<div class="table-results-pager">
		<ul>
		{foreach from=$result.data.rows.pager item=$page_no key=$page_i}
			<li>
			{if $page_i == $result.data.rows.page_current}
				<span class="table-results-pager-current">{$page_no}</span>
			{else}
				<a href="?page={$page_i}">{$page_no}</a>
			{/if}
			</li>
		{/foreach}
		</ul>
		<div class="both"></div>
	</div>
	{/if}
</div>
{/if}
{/nocache}