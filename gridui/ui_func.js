/*
v1.03 Copyright (c) 2006 Stuart Colville
http://muffinresearch.co.uk/archives/2006/04/29/getelementsbyclassname-deluxe-edition/

The function has three parameters:
strClass:    
         string containing the class(es) that you are looking for
strTag (optional, defaults to '*') :
         An optional tag name to narrow the search to specific tags e.g. 'a' for links.
objContElm (optional, defaults to document):
         An optional object container to search inside. Again this narrows the scope of the search 
*/            
function getElementsByClassName(strClass, strTag, objContElm) {
  strTag = strTag || "*";
  objContElm = objContElm || document;    
  var objColl = objContElm.getElementsByTagName(strTag);
  if (!objColl.length &&  strTag == "*" &&  objContElm.all) objColl = objContElm.all;
  var arr = new Array();                              
  var delim = strClass.indexOf('|') != -1  ? '|' : ' ';   
  var arrClass = strClass.split(delim);    
  for (var i = 0, j = objColl.length; i < j; i++) {                         
    var arrObjClass = objColl[i].className.split(' ');   
    if (delim == ' ' && arrClass.length > arrObjClass.length) continue;
    var c = 0;
    comparisonLoop:
    for (var k = 0, l = arrObjClass.length; k < l; k++) {
      for (var m = 0, n = arrClass.length; m < n; m++) {
        if (arrClass[m] == arrObjClass[k]) c++;
        if ((delim == '|' && c == 1) || (delim == ' ' && c == arrClass.length)) {
          arr.push(objColl[i]); 
          break comparisonLoop;
        }
      }
    }
  }
  return arr; 
}

function set_cookie(name, value) {
	var nextyear = new Date();
	nextyear.setFullYear(nextyear.getFullYear() + 1);
	var curCookie = name + "=" + escape(value) + "; expires=" + nextyear.toGMTString();
	document.cookie = curCookie;
	return true;
}
function get_cookie(name) {
	var prefix = name + "=";
	var cookieStartIndex = document.cookie.indexOf(prefix);
	if(cookieStartIndex == -1) return "";
	var cookieEndIndex = document.cookie.indexOf(";", cookieStartIndex + prefix.length);
	if(cookieEndIndex == -1) cookieEndIndex = document.cookie.length;
	return unescape(document.cookie.substring(cookieStartIndex + prefix.length, cookieEndIndex));
}

function hasClass(elem, className) {
	return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className)
}


function menu_comand(id, name, par){
        this.id = id;
	this.name = name;
        this.par = par;
}

// Эта функция нужна из-за проблем IE при создании
// INPUT элемента, требующего наличие атрибута NAME
function createNamedElement(type, name) {
   var element = null;
   // Try the IE way; this fails on standards-compliant browsers
   try {
      element = document.createElement('<'+type+' name="'+name+'">');
   } catch (e) {
   }
   if (!element || element.nodeName != type.toUpperCase()) {
      // Non-IE browser; use canonical method to create named element
      element = document.createElement(type);
      element.name = name;
   }
   return element;
}

// Аналогичная функция, но для внешнего document
//
function createDocNamedElement(doc, type, name) {
   var element = null;
   // Try the IE way; this fails on standards-compliant browsers
   try {
       element = doc.createElement('<'+type+' name="'+name+'">');
   } catch (e) {
   }
   if (!element || element.nodeName != type.toUpperCase()) {
      // Non-IE browser; use canonical method to create named element
      element = doc.createElement(type);
      element.name = name;
   }
   return element;
}

// Функция подбора правильной формы слова
// На входе принимаем 2 переменные
// num - число
// arr - массив с формами слов
function wordForms(n, arr) {
    var i;
    n = parseInt(n);
    var dten = n % 10;
    var dhun = n % 100;
    i = (dten==1 && dhun!=11 ? 0 : dten>=2 && dten<=4 && (dhun<10 || dhun>=20) ? 1 : 2); 
    return arr[i];
}

var jobsWordForms = [" активное задание"," активных задания"," активных заданий"];
//var sitesWordForms = [" центр"," центра", " центров"];
var sitesWordForms = [" ресурс"," ресурса", " ресурсов"];
var hoursWordForms = [" час "," часа ", " часов "];
var minutesWordForms = [" минута "," минуты ", " минут "];
var secondsWordForms = [" секунда "," секунды ", " секунд "];
var tasksWordForms = [" задача"," задачи"," задач"];


function parse_sec(sec){
	var str;
	var tmp = Math.floor(sec/3600);
	str = tmp + wordForms(tmp, hoursWordForms);
	tmp = Math.floor((sec % 3600)/60);
	str += tmp + wordForms(tmp, minutesWordForms);
	tmp = sec % 60;
	str += tmp + wordForms(tmp, secondsWordForms);
	return str;
}

function htmlspecialchars(html) {
      // Сначала необходимо заменить &
      html = html.replace(/&/g, "&amp;");
      // А затем всё остальное в любой последовательности
      html = html.replace(/</g, "&lt;");
      html = html.replace(/>/g, "&gt;");
      html = html.replace(/"/g, "&quot;");
      // Возвращаем полученное значение
      return html;
}

function get_site_list(){
	var req = new Array();
	req["jdl"]="";
	start_busy("siteList");
	return ui_req_a(req_cb_list, ada, "siteList", req);  //return unique ID of the command
}

function reset_site_list(res, tid){
	var oRow, oCell, oRadio;
	var oTBody = document.getElementById(tid).tBodies[0];
	var oFoot = document.getElementById(tid).tFoot;
	var i;

// delete old table content
	while(oTBody.rows.length > 0){
		oTBody.deleteRow(0);
	}
// clear '_out' div
        var e = document.getElementById(ui_div(res["cmd"]) + "_out");
	var t_out = e.getElementsByTagName('TABLE')[0];
	if (t_out){
	    t_out.parentNode.removeChild(t_out);
	}
	    
	var k = res["siteCount"];
	if (k != 0){
		for ( i=0; i<k; i++){
			oRow = document.createElement("TR");
			oTBody.appendChild(oRow);

// split site name
			var fields = res["siteName" +eval(i+1)].split('/');
// insert host name
			oCell = document.createElement("TD");
			oCell.innerHTML = fields[0];
			oRow.appendChild(oCell);
// insert scheduler
			oCell = document.createElement("TD");
			oCell.innerHTML = fields[2];
			oRow.appendChild(oCell);
// insert queue name
			oCell = document.createElement("TD");
			oCell.style.width = "50%";
			oCell.innerHTML = fields[1];
			oRow.appendChild(oCell);
		}
	}
	oFoot.firstChild.firstChild.innerHTML="Всего " +k + wordForms(k, sitesWordForms);
	return true;
}

function get_site_info(site_name){
	var req = new Array();
	req["siteCount"] = 1;
	req["siteName1"] = site_name;
	start_busy("siteInfo");
	return ui_req_a(req_cb_list, ada, "siteInfo", req);  //return unique ID of the command
}


function get_site_listmatch(lid, dir){
	var fname=document.getElementById(lid);
	var req = new Array();
	var value = (fname.options) ? fname.options[fname.selectedIndex].value : fname.value;
	if (!value){
    	    alert ("Не указан файл описания задания");
            return true;
	}                                	
        req["jdl"]=dir +"-" + ada +"/" + value;
	start_busy("siteListMatch");
	return ui_req_a(req_cb_list, ada, "siteListMatch", req);  //return unique ID of the command
}

function get_job_info(job){
	var req = new Array();
	req["jobID"] = job.id;
	start_busy("jobLog");
	return ui_req_a(req_cb_list, ada, "jobLog", req);  //return unique ID of the command
}

function get_task_info(job,task){
	var req = new Array();
	req["jobID"] = job.id;
	req["taskName"] = task.name;
	start_busy("taskLog");
	return ui_req_a(req_cb_list, ada, "taskLog", req);  //return unique ID of the command
}

function reset_log(res,type){
	var out = document.getElementById(ui_div(res["cmd"]) + "_out");
	var jtype = type + "Log";
	var rep = /\n/gm;	
	out.innerHTML = res[jtype].replace(rep,"<br>");
	return true;
}

function preview_obj_file(obj) {
	if (typeof obj.text() == "string"){
		var wnd = window.open("", "_blank", "menubar=no,toolbar=no,location=no,directories=no,status=no,resizeable,scrollbars,width=600,height=400,left=100,top=100");
		if (wnd){
			wnd.document.open();
			wnd.document.writeln("<html><body><pre>");
			wnd.document.writeln(obj.text());
			wnd.document.writeln("</pre></body></html>");
			wnd.document.close();
			wnd.focus();
			return true
		}
	}
}

function load_vo_list(lid){
// lid - id of list of virt. org.
        var l = document.getElementById(lid);
        var ent, opt;
        l.disabled = true;
        while(l.length > 0) {
            l.remove(0);
        }
                                                    
        var res=ui_req_s(ada, "credVOList", null);
	var out = document.getElementById(ui_div("credVOList") +"_out");
	out.innerHTML = "";
// если ошибка, то вывести сообщение
        if (res["status"] != 0){
		out.innerHTML = res["retMsg"] + "\n";
		if (res["retNative"]) out.innerHTML += "Причина : " + htmlspecialchars(res["retNative"]);
        }
// и в любом случае заполнить список ВО
	opt = document.createElement("OPTION");
	opt.text = "";
	opt.value = "";
	try {
                l.add(opt, null);
	} catch(e) {
                l.add(opt);
	}
	var oldval = get_cookie("gridui-vo");
	for ( var j=0; j<res["voCnt"]; j++){
                opt = document.createElement("OPTION");
                opt.text = res["voName" + eval(j+1)];
		opt.value = opt.text;
		if(opt.value == oldval) opt.selected = true;
                try {
                    l.add(opt, null);
                } catch(e) {
                    l.add(opt);
                }
	}
	l.disabled = false;
        return true;
}

function set_vo_name(lid,vo){
// lid - id of list of virt. org.
// vo  - VO name 
        var el = document.getElementById(lid);
	for (var k=0; k<el.childNodes.length; k++){
		if (el.childNodes[k].value == vo){
			set_cookie("gridui-vo", vo);
			el.childNodes[k].selected = "true";
			break;
		}
	}
}