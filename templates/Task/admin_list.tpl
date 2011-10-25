
{a href=admin/content/task/new text="Добавить запись"}

{$pagination}

{if $collection}
	<table class="std-grid tr-highlight">
	<tr>
		<th>{$sorters.id}</th>
		<th>{$sorters.uid}</th>
		<th>{$sorters.name}</th>
		<th>{$sorters.xrsl_command}</th>
		<th>{$sorters.state}</th>
		<th>{$sorters.date}</th>
		<th>Опции</th>
	</tr>
	{foreach from=$collection item='item'}
	<tr>
		<td>{$item.id}</td>
		<td>{$item.uid}</td>
		<td>{$item.name}</td>
		<td>{$item.xrsl_command}</td>
		<td>{$item.state}</td>
		<td>{$item.date}</td>
		<td>
			<div class="tr-hover-visible options">
				<a href="{a href=task/view/`$item.id`}" class="item" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="{a href=admin/content/task/edit/`$item.id`}" class="item" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="{a href=admin/content/task/delete/`$item.id`}" class="item" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	{/foreach}
	</table>
{else}
	<p>Сохраненных записей пока нет.</p>
{/if}

{$pagination}

