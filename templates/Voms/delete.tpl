
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить ВО <b>{$name}</b> безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="{$instanceId}" />
			{$formcode}
			
			<input class="button" type="submit" name="action[voms/delete]" value="Удалить" />
			<a class="button" href="{a href=admin/content/voms/list}">Отмена</a>
		</form>
	</div>
	
</div>
