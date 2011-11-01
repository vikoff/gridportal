
<div align="right" style="margin-right: 50px;">
	<a href="{a href=task/new}">{lng snippet='tasklist.add-new'}</a>
</div>

{$pagination}

{if $collection}
	<table class="std-grid tr-highlight">
	<tr>
		<th>{$sorters.name}</th>
		<th>{lng snippet='tasklist.xrsl-presence'}</th>
		<th>{$sorters.state_title}</th>
		<th>{$sorters.date}</th>
		<th colspan="10">{lng snippet='options'}</th>
	</tr>
	{foreach from=$collection item='item'}
	<tr>
		<td>{$item.name} {if $item.is_test}<sup>{lng snippet='task.delete-2'}</sup>{/if}</td>
		<td>{if $item.is_gridjob_loaded}<span class="green">{lng snippet='TaskList.present'}</span>{else}<span class="red">{lng snippet='TaskList.no'}</span>{/if}</td>
		<td>{lng snippet=$item.state_title}</td>
		<td>{$item.date_str}</td>
		
			<td class="option">{if $item.actions.rename}<a href="{a href=task/edit/`$item.id`}" class='button-small'>{lng snippet='rename'}</a>{/if}</td>
			<td class="option">{if $item.actions.file_manager}<a href="{a href=task/upload-files/`$item.id`}" class='button-small'>{lng snippet='task.files'}</a>{/if}</td>
			<td class="option">{if $item.actions.run}<a href="{a href=task/xrsl-edit/`$item.id`}" class='button-small'>{lng snippet='task.run'}</a>{/if}</td>
			<td class="option">{if $item.actions.stop}<a href="{a href=task/stop/`$item.id`}" class='button-small'>{lng snippet='task.stop'}</a>{/if}</td>
			<td class="option">{if $item.actions.get_results}<a href="{a href=task/get-results/`$item.id`}" class='button-small'>{lng snippet='task.get-result'}</a>{/if}</td>
			<td class="option">{if $item.actions.delete}<a href="{a href=task/delete/`$item.id`}" class='button-small'>{lng snippet='task.delete'}</a>{/if}</td>
		</td>
	</tr>
	{/foreach}
	</table>
{else}
	<p>{lng snippet='tasklist.no-task'}</p>
{/if}

{$pagination}
