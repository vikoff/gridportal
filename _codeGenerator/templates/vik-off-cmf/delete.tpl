<p>

	Хотите удалить запись #{$instanceId}

<? foreach($FIELDS_TITLES as $field => $title)
	echo "\t".$title.': {$'.$field."}, \n"; ?>
		
	безвозвратно?

	<form action="" method="post">
		<input type="hidden" name="id" value="{$instanceId}" />
		{$formcode}
		
		<input class="button" type="submit" name="action[<?=$MODEL_NAME_LOW;?>/delete]" value="Удалить" />
		<a class="button" href="{a href=admin/content/<?=$MODEL_NAME_LOW;?>/list}">Отмена</a>
	</form>

</p>