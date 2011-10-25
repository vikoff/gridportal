<script type="text/javascript">
$(function() {

	var fm = new FileManager(
		'task/get-task-files/{{$id}}',
		'task/delete-task-file/{{$id}}',
		$('#task-uploaded-files-container'),
		$('#task-uploaded-files-comment')
	);
	
	fm.getFiles();
	
});
</script>

<h2>{lng snippet='upload_files.uploadfilemsg'}</h2>

<div class="task-uploaded-files">
	<div style="font-weight: bold; text-align: center; margin-bottom: 10px;">{lng snippet='upload_files.uploadfiles'}</div>
	<form action="" method="post" enctype="multipart/form-data">
		{$formcode}
		<input type="hidden" name="action" value="task/upload-file" />
		<input type="hidden" name="id" value="{$id}" />
		<input type="file" name="Filedata" />
		<input type="submit" value="Отправить" />
	</form>
</div>

<div class="task-uploaded-files">
	<div style="font-weight: bold; text-align: center; margin-bottom: 10px;">{lng snippet='upload_files.sendfileslist'}</div>
	<div id="task-uploaded-files-container"></div>
	<div id="task-uploaded-files-comment" style="margin-top: 1em;"></div>

</div>
<div class="paragraph">
	<span id="task-run-button" style="display: none;">{a href=task/xrsl-edit/$id class="button" text="Перейти к запуску"}</span>
	{a href=task class="button" text="Вернуться к списку задач"}
</div>

