
var wndPop = null;
var wndCB = null;

// Используемые аргументы:
// 	init_path - начальная директория
// 	path - путь к файлу относительно init_path
// 	fpath - id поля с путём к файлу
// 	fcontent - id поля с содержимым файла или строка с именем объекта
//	func - имя функции, которая вызывается по закрытию окна (успешному)
//	btntext - текст на кнопке нового окна (по умолчанию btntext="Выбрать")


// вызов функции file_select приводит к появлению нового окна c диалогом выбора файла
// в результате закрытия этого окна в случае успешного завершения вызывается 
// call-back функция, указанная в arg.func. Это может быть либо file_open, 
// либо file_save (для случая save as)
// варианты строки для вызова:
// для текстового редактора:  fcontent - id поля с содержимым файла
//	"file_select({init_path:\"dag-" + ada + "/\",fpath:\"file_dag_name\",fcontent:\"file_dag_content\",func:\"file_open\",btntext:\"Открыть\"})"
// для глобального объекта:  fcontent - строка с именем объекта
//	"file_select({init_path:\"dag-" + ada + "/\",fpath:\"edit_dag_name\",fcontent:\"dag\",func:\"file_open\",btntext:\"Открыть\"})"
// для получения только пути к файлу в текстовое поле с id=arg.fpath (fcontent отсутствует в аргументах)
//	 "file_select({init_path:\"dag-" + ada + "/\",fpath:\"file_jdl_name\",func:\"file_open\"})"


function file_select(arg) {
	if (wndPop) {
	    wndCB = null;
	    wndPop.close();
	}
	var str = "uw_cat_frame.html?init_path=" + arg.init_path;
	if (arg.func) str += "&func=" + arg.func;
	if (arg.fpath) str += "&fpath=" + arg.fpath;
	if (arg.fcontent) str += "&fcontent=" + arg.fcontent;
	if (arg.btntext) str += "&btntext=" + arg.btntext;
	if (arg.path) str += "&path=" + arg.path;
	wndPop = window.open(str, "_blank", "menubar=no,toolbar=no,location=no,directories=no,status=no,resizable=yes,width=340,height=400,left=100,top=100");
	if (wndPop){
		if(arg.cb) wndCB = arg.cb;
		return true;
	} else {
		alert ("Не удаётся открыть окно для выбора файла.\nПроверьте настройки браузера.");
		return false;
	}
}


function file_open(arg){
	var t = document.getElementById(arg.fpath);
	if (arg.fcontent){
		var s = document.getElementById(arg.fcontent);
		if (s){								// текстовый редактор
		        if(s.name == "changed" && confirm("Файл " + t.value + " был изменён.\nСохранить изменения?")) {
				file_save({init_path:arg.init_path, fpath:arg.fpath, fcontent:arg.fcontent });
			}
			s.name = "";
			t.value = arg.path;
			var data = ui_file_load_s(arg.init_path + arg.path);
			if(typeof data == "string") {
				s.value = data;
				return true;
			} else {
				s.value = "";
		                alert("Ошибка загрузки файла: " + data);
				return false;
			}
		} else {
			var obj;
			eval("obj = " + arg.fcontent);
			if (obj){						//объект
	        		if(obj.changed && confirm("Файл " + t.value + " был изменён.\nСохранить изменения?")) {
					file_save({init_path:arg.init_path, fpath:arg.fpath, fcontent:arg.fcontent });
				}
				t.value = arg.path;
				var data = ui_file_load_s(arg.init_path + arg.path);
				if(typeof data == "string") {
					if (obj.load(data)) {
						obj.changed = false;
						return true;
					}
				}
				obj.clear();
				obj.changed = false;
				alert("Ошибка загрузки файла: " + data);
				return false;
			} else {						//ошибка
				alert("Неправильный аргумент вызова функции file_open");
				return false;
			}
		}
	} else {							// текстовое поле с путём к файлу
		if (arg.path) t.value = arg.path;
		return true;

	}

}


function file_save(arg){
        var t = document.getElementById(arg.fpath);
	var s = document.getElementById(arg.fcontent);

	if (s){								// текстовый редактор
		if (arg.path) t.value = arg.path;
		if (t.value){
			s.disabled = true;
			ui_file_save_s(arg.init_path + t.value, s.value);
			s.name = "";
			s.disabled = false;
		} else alert("Имя файла не определено");
		return true;
	} else {
		var obj;
		eval("obj = " + arg.fcontent);
		if (obj){						//объект
			if (arg.path) t.value = arg.path;
			if (t.value){
				if (obj.text()){
					ui_file_save_s(arg.init_path + t.value, obj.text());
				}
				obj.changed = false;
			} else alert("Имя файла не определено");
			return true;
		} else {						//ошибка
			alert("Неправильный аргумент вызова функции file_save");
			return false;
		}
	}
}


function file_reset(arg) {

var t = document.getElementById(arg.fpath);
var s = document.getElementById(arg.fcontent);

  try {
	if (s){								// текстовый редактор
	        if(s.name == "changed" && confirm("Файл " + t.value + " был изменён.\nСохранить изменения?")) {
			file_save({init_path:arg.init_path, fpath:arg.fpath, fcontent:arg.fcontent });
		}
		s.name = "";
		s.value = "";
		t.value = "";
		return true;
	} else {
		var obj;
		eval("obj = " + arg.fcontent);

		if (obj){						//объект
		        if(obj.changed && confirm("Файл " + t.value + " был изменён.\nСохранить изменения?")) {
				file_save({init_path:arg.init_path, fpath:arg.fpath, fcontent:arg.fcontent });
			}
			obj.clear();
			obj.changed = false;
			t.value = "";
			return true;
		} else {						//ошибка
			alert("Неправильный аргумент вызова функции file_reset");
			return false;
		}
	}

  } catch (e) {
	alert(e.message);
	return false;
  }
}


function file_remove(arg) {
var t = document.getElementById(arg.fpath);
var s = document.getElementById(arg.fcontent);
	if (s){								// текстовый редактор
		if (!t.value) return false;
		if (!confirm("Вы уверены, что хотите удалить выбранный файл?") ){
			 return false;
		}
		s.disabled = true;
		ui_file_remove_s(arg.init_path + t.value);
		s.name = "";
		s.value = "";
		s.disabled = false;
		t.value = "";
		return true;
	} else {
		var obj;
		eval("obj = " + arg.fcontent);
		if (obj){						//объект
			if (!t.value) return false;
			if (!confirm("Вы уверены, что хотите удалить выбранный файл?") ){
				 return false;
			}
			ui_file_remove_s(arg.init_path + t.value);
			obj.clear();
			obj.changed = false;
			t.value = "";
			return true;
		} else {						//ошибка
			alert("Неправильный аргумент вызова функции file_remove");
			return false;
		}
	}
}
