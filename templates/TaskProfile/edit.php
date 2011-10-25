
<ul id="submit-box-floating"></ul>

<h2>Редактирование профиля</h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />

	<div class="paragraph">
		<label class="title">Имя профиля</label>
		<input type="text" name="name" value="<?= $this->name; ?>" />
	</div>
	<div class="paragraph">
		<label class="title">Проект</label>
		<select name="project_id" />
			<? foreach($this->projects_list as $p): ?>
				<option value="<?= $p['id']; ?>" <? if($p['id'] == $this->project_id): ?>selected="selected"<? endif; ?>><?= $p['name']; ?></option>
			<? endforeach; ?>
		</select>
	</div>

	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[task-profile/save][admin/content/task-profile/<?= $this->instanceId ? 'list' : 'edit/(%id%)' ; ?>]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[task-profile/save][admin/content/task-profile/edit/<?= $this->instanceId ? $this->instanceId : '(%id%)' ; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="<?= href('admin/content/task-profile/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		<? if($this->instanceId): ?>		
			<a id="submit-delete" class="button" href="<?= href('admin/content/task-profile/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
			<a id="submit-copy" class="button" href="<?= href('admin/content/task-profile/copy/'.$this->instanceId); ?>" title="Сделать копию записи">копировать</a>
		<? endif; ?>		
	</div>
</form>
	
<? if($this->instanceId): ?>

	<h2>Файлы профиля</h2>
	<div class="task-uploaded-files">
		<div style="font-weight: bold; text-align: center; margin-bottom: 10px;"><?= Lng::get('upload_files.uploadfiles'); ?></div>
		<form action="" method="post" enctype="multipart/form-data">
			<?= FORMCODE; ?>	
			<input type="hidden" name="action" value="task-profile/upload-file" />
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<input type="file" name="Filedata" />
			<input type="submit" value="Отправить" />
		</form>
	</div>
	
	<div class="task-uploaded-files">
		<div style="font-weight: bold; text-align: center; margin-bottom: 10px;"><?= Lng::get('upload_files.sendfileslist'); ?></div>
		<div id="task-uploaded-files-container"></div>
		<div id="task-uploaded-files-comment" style="margin-top: 1em;"></div>
	</div>
	
	<script type="text/javascript">
	$(function() {

		var fm = new FileManager(
			'task-profile/get-task-files/<?= $this->instanceId; ?>',
			'task-profile/delete-task-file/<?= $this->instanceId; ?>',
			$('#task-uploaded-files-container'),
			$('#task-uploaded-files-comment')
		);
		
		fm.getFiles();
		
	});
	</script>
	
<? else: ?>

	Загрузка файлов будет доступна после сохранения профиля.
	
<? endif; ?>

<script type="text/javascript">

$(function(){
	$("#edit-form").validate( { <?= $this->validation; ?> } );
	enableFloatingSubmits();
});

</script>
