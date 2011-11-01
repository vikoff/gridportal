
<ul id="submit-box-floating"></ul>

<h2>{$pageTitle}</h2>

<form id="edit-form" action="" method="post">
	{$formcode}
	<input type="hidden" name="id" value="{$instanceId}" />

	<div class="paragraph">
		<label class="title">Название</label>
		<input type="text" name="name" value="{$name}" />
	</div>
	
	<div class="paragraph">
		<fieldset title="Виртуальные организации">
			<legend style="font-weight: bold;">Виртуальные организации</legend>
			{foreach from=$vomsList item='v'}
				<input id="checkbox-voms-{$v.id}" type="checkbox" name="voms[]" value="{$v.id}" {if $allowedVoms[$v.id]}checked="checked"{/if}>
				<label for="checkbox-voms-{$v.id}">{$v.name}</label><br />
			{/foreach}
		</fieldset>
	</div>
	
	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[project/save][admin/content/project/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[project/save][admin/content/project/edit/{if $instanceId}{$instanceId}{else}(%id%){/if}]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="{a href=admin/content/project/list}" title="Отменить все изменения и вернуться к списку">отмена</a>
		{if $instanceId}
		<a id="submit-delete" class="button" href="{a href=admin/content/project/delete/$instanceId}" title="Удалить запись">удалить</a>
		{/if}
		{if $instanceId}
		<a id="submit-copy" class="button" href="{a href=admin/content/project/copy/$instanceId}" title="Сделать копию записи">копировать</a>
		{/if}
	</div>
</form>

<script type="text/javascript">

$(function(){
	$("#edit-form").validate( { {{$validation}} } );
	enableFloatingSubmits();
});

</script>
