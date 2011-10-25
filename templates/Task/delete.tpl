<p>

	{lng snippet='task.delete-1'} <b>{$name}</b> {if $is_test}{lng snippet='task.delete-2'}{else}{lng snippet='task.delete-3'}{/if}?

	<form action="" method="post">
		<input type="hidden" name="id" value="{$instanceId}" />
		{$formcode}
		
		<input class="button" type="submit" name="action[task/delete]" value="{lng snippet='task.delete-4'}" />
		<a class="button" href="{a href=task/list}">{lng snippet='task.delete-5'}</a>
	</form>

</p>