
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />

	<div class="paragraph">
		<label class="title">myproxy.name</label>
		<input type="text" name="name" value="<?= $this->name; ?>" />
	</div>
	<div class="paragraph">
		<label class="title">myproxy.url</label>
		<input type="text" name="url" value="<?= $this->url; ?>" />
	</div>
	<div class="paragraph">
		<label class="title">myproxy.port</label>
		<input type="text" name="port" value="<?= $this->port; ?>" />
	</div>

	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[myproxy-server/save][admin/content/myproxy-server/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[myproxy-server/save][admin/content/myproxy-server/edit/<?= $this->instanceId ? $this->instanceId : '(%id%)' ; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="<?= href('admin/content/myproxy-server/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		<? if($this->instanceId): ?>		
			<a id="submit-delete" class="button" href="<?= href('admin/content/myproxy-server/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
			<a id="submit-copy" class="button" href="<?= href('admin/content/myproxy-server/copy/'.$this->instanceId); ?>" title="Сделать копию записи">копировать</a>
		<? endif; ?>		
	</div>
</form>

<script type="text/javascript">

$(function(){
	$("#edit-form").validate( { <?= $this->validation; ?> } );
	enableFloatingSubmits();
});

</script>
