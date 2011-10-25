
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #{$instanceId}

		id: {$id}, 
		Название: {$name}, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="{$instanceId}" />
			{$formcode}
			
			<input class="button" type="submit" name="action[project/delete]" value="Удалить" />
			<a class="button" href="{a href=admin/content/project/list}">Отмена</a>
		</form>
	</div>
	
</div>
