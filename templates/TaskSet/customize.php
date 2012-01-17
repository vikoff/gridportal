<h2 style="vertical-align:bottom;">
	<img src="/images/icons/data.gif" alt="<?= Lng::get('task-set-customize.fails') ?>" title="<?= Lng::get('task-set-customize.fails') ?>" align="center" width=32 height=32 onmouseover="this.src='/images/icons/data.a.gif'" onmouseout="this.src='/images/icons/data.gif'" />
	<?= Lng::get('task-set-customize.fails') ?>
</h2>

<div class="task-uploaded-files">
	<div style="font-weight: bold; text-align: center; margin-bottom: 10px;"><?= Lng::get('upload_files.uploadfiles'); ?></div>
	<form action="" method="post" enctype="multipart/form-data">
		<?= FORMCODE; ?>	
		<input type="hidden" name="action" value="task-set/upload-file" />
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

<div class="paragraph c">

	<a href="<?= href('task-set/submit/'.$this->instanceId); ?>" class="button"><?= Lng::get('upload_files.go-to-start'); ?></a>
	<a href="<?= href('task-set/view/'.$this->instanceId) ?>" class="button"><?= Lng::get('upload_files.returne-to-task'); ?></a>
	<a href="<?= href('task-set/list') ?>" class="button"><?= Lng::get('upload_files.returne-to-list'); ?></a>
</div>

<script type="text/javascript">
	var FileManager = {
		
		editUrl: 'task-set/edit-file/<?= $this->instanceId; ?>',
		constructUrl: 'task-set/file-constructor/<?= $this->instanceId; ?>',
		getUrl: href('task-set/get-task-files/<?= $this->instanceId; ?>'),
		delUrl: href('task-set/delete-task-file/<?= $this->instanceId; ?>'),
		htmlContainer: $('#task-uploaded-files-container'),
		htmlComment: $('#task-uploaded-files-comment'),
		
		hasNordujob: false,
		
		getFiles: function(){
			
			var self = this;
			
			$.get(this.getUrl, function(response){
				
				if (!response) {
					trace('Ошибка получения данных');
					return;
				}
				
				if(response.error){
					self.htmlContainer.empty().html('<div style="color: red;">' + response.error + '</div>');
					return;
				}
				
				var tbl = $('<table class="task-uploaded-files-list" />');
				var delLink, editLink, constructorLink;
				for(var i in response.data){
				
					type = '';
					if(response.data[i].name == 'nordujob'){
						type = '<?= Lng::get('task-set.nordujob-file'); ?>';
						self.hasNordujob = true;
					}
					else if(/\.fds$/.test(response.data[i].name))
						type = '<?= Lng::get('task-set.model-file'); ?>';
					
					editLink = (function(file){
						var url = href(self.editUrl + '?file=' + encodeURIComponent(file));
						return $('<a href="' + url + '" class="small" target="_blank"><?= Lng::get('task-set.edit'); ?></a>')
							.click(function(){ self.editFile(url); return false; });
					})(response.data[i].name);
						
					delLink = (function(file){
						return $('<a href="#" class="small"><?= Lng::get('task-set.delete'); ?></a>')
							.click(function(){ self.removeFile(file); return false; });
					})(response.data[i].name);
					
					constructorLink = response.data[i].type
						? (function(file){
								var url = href(self.constructUrl + '?file=' + encodeURIComponent(file));
								return $('<a href="' + url + '" class="small" target="_blank"><?= Lng::get('task-set.edit-in-master'); ?></a>')
									.click(function(){ self.constructFile(url); return false; });
							})(response.data[i].name)
						: null;
					tbl.append(
						$('<tr />')
							.append('<td>' + (type ? '<span class="small" style="color: #888;">' + type + '</span>' : '') + '</td>')
							.append('<td>' + response.data[i].name + '</td>')
							.append($('<td></td>').append(editLink))
							.append($('<td></td>').append(delLink))
							.append( constructorLink ? $('<td></td>').append(constructorLink) : $('<td></td>') )
					);
				}
				
				self.htmlContainer.empty().append(tbl);
				
				if(self.hasNordujob){
					self.htmlComment.html('<span class="small green"><?= Lng::get('task-set.nordujob-file-loaded'); ?></span>');
				}else{
					self.htmlComment.html('<span class="small red"><?= Lng::get('task-set.nordujob-file-not-loaded'); ?></span>');
				}
					
			}, 'json');
		},
		
		editFile: function(url){
			
			var iframe = $('<iframe src="' + url + '" style="width: 800px; height: 500px;" />');
			$.modal($('<div />').append(iframe));
		},
		
		removeFile: function(name){
			
			if(!confirm('<?= Lng::get('task-set.delete-file'); ?> "' + name + '"?'))
				return;
			
			var self = this;
			
			$.post(this.delUrl, {file: name}, function(response){
				
				if(response != 'ok')
					alert(response);
					
				self.getFiles();
			});
		},
		
		constructFile: function(url){
			
			var iframe = $('<iframe src="' + url + '" style="width: 800px; height: 500px;" />');
			$.modal($('<div />').append(iframe));
		},
	};
$(function() {
	
	
	FileManager.getFiles();
	
});
</script>
