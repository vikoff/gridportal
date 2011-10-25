<link href="css/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript" src="js/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">

function getTaskFiles(){
	$.get('?r=task/get-task-files/{{$id}}', function(response){
		
		if(response.error){
			$('#task-uploaded-files-container').empty().html('<div style="color: red;">' + response.error + '</div>');
			return;
		}
		
		var tbl = $('<table class="task-uploaded-files-list" />');
		var hasNordujob = false;
		var delLink = null;
		for(var i in response.data){
		
			type = '';
			if(response.data[i] == 'nordujob'){
				type = 'nordujob файл';
				hasNordujob = true;
			}
			else if(/\.fds$/.test(response.data[i]))
				type = 'файл модели';
				
			delLink = $('<a href="#" class="small" onclick="deleteTaskFile(\'' + response.data[i] + '\'); return false;">удалить</a>');
			
			tbl.append(
				$('<tr />')
					.append('<td>' + (type ? '<span class="small" style="color: #888;">' + type + '</span>' : '') + '</td>')
					.append('<td>' + response.data[i] + '</td>')
					.append($('<td></td>').append(delLink)));
		}
		
		$('#task-uploaded-files-container').empty().append(tbl);
		
		if(hasNordujob){
			$('#task-uploaded-files-comment').html('<span class="small green">Файл nordujob загружен</span>');
			$('#task-run-button').show();
		}else{
			$('#task-uploaded-files-comment').html('<span class="small red">Файл nordujob не загружен</span>');
			$('#task-run-button').hide();
		}
			
	}, 'json');
}

function deleteTaskFile(name){

	if(!confirm('Удалить файл "' + name + '"?'))
		return;
		
	$.post('?r=task/delete-task-file/{{$id}}', {file: name}, function(response){
		if(response != 'ok')
			alert(response);
		getTaskFiles();
	});
}

$(function() {

	getTaskFiles();
	
	$('#file_upload').uploadify({
		'uploader'  : '{{$WWW_ROOT}}includes/uploadify/uploadify.swf',
		'script'    : '{{$WWW_ROOT}}includes/uploadify/uploadify.php',
		'cancelImg' : '{{$WWW_ROOT}}images/uploadify/cancel.png',
		'auto'      : true,
		'multi'		: true,
		'scriptData': {
			'id': {{$id}},
			'PHPSESSID': '{{$session_id}}',
			'action': 'task/upload-file',
			'allowDuplication': 1
		},
		'onComplete': function(event, id, file, response, data){
			getTaskFiles();
			if(response != 'ok')
				alert(response);
		}
	});
	
});
</script>

<h2>Загрузка файлов</h2>

<div class="paragraph">
	<form action="" method="post" enctype="multipart/form-data" style="width: 400px; margin: auto;">
		Загрузите файлы, связанные с задачей.<br />
		Одновременно можно загрузжать несколько файлов.<br /><br />
		<input type="file" id="file_upload" name="file" />
	</form>
</div>

<div class="task-uploaded-files">
	<div style="font-weight: bold; text-align: center; margin-bottom: 10px;">Список загруженных файлов</div>
	<div id="task-uploaded-files-container"></div>
	<div id="task-uploaded-files-comment" style="margin-top: 1em;"></div>

</div>
<div class="paragraph">
	<span id="task-run-button" style="display: none;">{a href=task/xrsl-edit/$id class="button" text="Перейти к запуску"}</span>
	{a href=task class="button" text="Вернуться к списку задач"}
</div>

