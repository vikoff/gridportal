
<form id="edit-form" action="" method="post">
	{$formcode}
	<input type="hidden" name="id" value="{$instanceId}" />
	
	<h2>
		{if !$instanceId}
			{lng snippet='edit.addNewTask'}
		{else}
			{lng snippet='edit.renameTask'} #{$instanceId}
		{/if}
	</h2>
	
	<div class="paragraph">
		{lng snippet='edit.name'}
		<input type="text" name="name" value="{$name}" />
	</div>
	
	{if !$instanceId}
	<div class="paragraph">
		<input id="checkbox-is_test" type="checkbox" name="is_test" value="1" {if $is_test}checked="checked"{/if} />
		<label for="checkbox-is_test">{lng snippet='task.delete-2'}</label> 
        
        <img src="images/help.png" align="justify" alt="{lng snippet='edit.help-alt'}" title="{lng snippet='edit.help-test-task'}" width=20 height=20> 
        
	</div>
	{/if}
	
	<div class="paragraph">
		<input class="button" type="submit" name="action[task/{if $instanceId}rename{else}save{/if}][]" value="{if $instanceId}{lng snippet='save'}{else}{lng snippet='xrls_edit.get'}{/if}" />
		<a class="button" href="{a href=task/list}">{lng snippet='task.delete-5'}</a>
	</div>
</form>

<script type="text/javascript">

$(function(){

});

</script>
