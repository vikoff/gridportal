
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #{$instanceId}

		id: {$id}, 
		Название: {$name}, 
		Проект: {$project_id}, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="{$instanceId}" />
			{$formcode}
			
			<input class="button" type="submit" name="action[software/delete]" value="Удалить" />
			<a class="button" href="{a href=admin/content/software/list}">Отмена</a>
		</form>
	</div>
	
</div>
