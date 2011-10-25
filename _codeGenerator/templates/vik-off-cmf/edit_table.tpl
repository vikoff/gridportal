
<form id="edit-form" action="" method="post">
	{$formcode}
	<input type="hidden" name="id" value="{$instanceId}" />
	
	<table class="stdItemEdit">
	<tr>
		<th class="title" colspan="2">
			{if !$instanceId}
				Создание новой записи
			{else}
				Редактирование записи #{$instanceId}
			{/if}
		</th>
	</tr>
<?
foreach($FIELDS_TITLES as $field => $title){
	if($field != 'id'){
		echo
'	<tr>
		<th>'.$title.'</th>
		<td><input type="text" name="'.$field.'" value="{$'.$field.'}" /></td>
	</tr>
';	}
}
?>
	<tr>
		<th>Действия</th>
		<td class="actions">
		
			<input class="button" type="submit" name="action[<?=$MODEL_NAME_LOW;?>/save]" value="Сохранить" />
			<a class="button" href="{a href=admin/content/<?=$MODEL_NAME_LOW;?>/list}">отмена</a>
			<a class="button" href="{a href=admin/content/<?=$MODEL_NAME_LOW;?>/delete/$instanceId}">удалить</a>
			
			<div class="after-action">
				+ 
				<select name="redirect" id="next-action-select">
					<option value="{a href=admin/content/<?=$MODEL_NAME_LOW;?>/list}">К адм. списку записей</option>
					<option value="{a href=admin/content/<?=$MODEL_NAME_LOW;?>/edit/$instanceId}">Продолжить редактирование</option>
					<option value="{a href=admin/content/<?=$MODEL_NAME_LOW;?>/new}">Создать новую запись</option>
				</select>
			</div>
			
		</td>
	</tr>
	<tr>
		<td class="footer" colspan="2">
			<a href="mailto:yurijnovikov@gmail.com" title="Разработчик: Юрий Новиков">vik-off CMF</a>
		</td>
	</tr>
	</table>
</form>

<script type="text/javascript">

$(function(){
	
	$("#next-action-select").val("{{$redirect}}");
	
	$("#edit-form").validate({{$validation}});

});

</script>
