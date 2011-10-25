
// набор стандартных опций для tinymce
function getDefaultTinyMceSettings(WWW_ROOT){
	
	return {
	
		script_url : WWW_ROOT + 'includes/tiny_mce/tiny_mce.js',
		
		language : 'ru',
		
		theme : "advanced",
		plugins : "pagebreak,emotions,inlinepopups,preview,media,contextmenu,paste,fullscreen,template,advlist",
		
		class_filter : function(cls, rule) {
			// trace(cls + ', ' + rule + '<br />');
			return cls;
		},
		
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,styleselect,formatselect,fontselect,fontsizeselect,|,undo,redo,|,preview,fullscreen",
		theme_advanced_buttons2 : "hr,removeformat,|,charmap,emotions,iespell,media,advhr,|,cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,image,cleanup,help,code,|,forecolor,backcolor,pagebreak",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		content_css : WWW_ROOT + "css/backend.css"
	};
}

function href(href){
	
	return WWW_ROOT + CUR_LNG + '/' + href;
}

function FileManager(getUrl, delUrl, htmlContainer, htmlComment){
	
	this.getUrl = href(getUrl);
	this.delUrl = href(delUrl);
	this.htmlContainer = htmlContainer;
	this.htmlComment = htmlComment;
	
	this.hasNordujob = false;
}
FileManager.prototype = {
	
	getFiles: function(){
		
		var self = this;
		
		$.get(this.getUrl, function(response){
			
			if(response.error){
				self.htmlContainer.empty().html('<div style="color: red;">' + response.error + '</div>');
				return;
			}
			
			var tbl = $('<table class="task-uploaded-files-list" />');
			var delLink = null;
			for(var i in response.data){
			
				type = '';
				if(response.data[i] == 'nordujob'){
					type = 'nordujob файл';
					self.hasNordujob = true;
				}
				else if(/\.fds$/.test(response.data[i]))
					type = 'файл модели';
					
				delLink = (function(file){
					return $('<a href="#" class="small">удалить</a>')
						.click(function(){ self.removeFile(file); return false; });
				})(response.data[i]);
				
				tbl.append(
					$('<tr />')
						.append('<td>' + (type ? '<span class="small" style="color: #888;">' + type + '</span>' : '') + '</td>')
						.append('<td>' + response.data[i] + '</td>')
						.append($('<td></td>').append(delLink)));
			}
			
			self.htmlContainer.empty().append(tbl);
			
			if(self.hasNordujob){
				self.htmlComment.html('<span class="small green">Файл nordujob загружен</span>');
			}else{
				self.htmlComment.html('<span class="small red">Файл nordujob не загружен</span>');
			}
				
		}, 'json');
	},
	
	removeFile: function(name){
		
		if(!confirm('Удалить файл "' + name + '"?'))
			return;
		
		var self = this;
		
		$.post(this.delUrl, {file: name}, function(response){
			
			if(response != 'ok')
				alert(response);
				
			self.getFiles();
		});
	}
};

$(function(){
	
	VikDebug.init();
	
	$.ajaxSetup({
		// error: function(xhr){VikDebug.print(xhr.responseText, 'ajax-error', {position: 'top'});}
		error: function(xhr){trace('ajax error:<br />' + xhr.responseText);}
	});
	
	$('table.tr-highlight>tbody>tr').hover(
		function(){$(this).addClass("tr-hover")},
		function(){$(this).removeClass("tr-hover");}
	);					

});
