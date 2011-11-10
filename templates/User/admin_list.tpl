
{$pagination}

{if $collection}
	<table class="std-grid tr-highlight">
	<tr>
		<th>{$sorters.id}</th>
		<th>{$sorters.email}</th>
		<th>{$sorters.surname}</th>
		<th>{$sorters.dn}</th>
		<th>{$sorters.dn_cn}</th>
		<th>{$sorters.level}</th>
		<th>{$sorters.regdate}</th>
		<th>Опции</th>
	</tr>
	{foreach from=$collection item='item'}
	<tr>
		<td>{$item.id}</td>
		<td>{$item.email}</td>
		<td>{$item.fio}</td>
		<td>{$item.dn}</td>
		<td>{$item.dn_cn}</td>
		<td>{$item.level}</td>
		<td>{$item.regdate}</td>
		<td style="font-size: 11px;">
			{a href=admin/users/view/`$item.id` text="Подробней"}
		</td>
	</tr>
	{/foreach}
	</table>
{else}
	<p>Сохраненных записей пока нет.</p>
{/if}

{$pagination}

