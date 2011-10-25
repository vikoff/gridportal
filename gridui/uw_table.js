function uw_job(parent,  type, outdir) {
// parent -  job for task, null for job
// type - job or task
	this.type = type;
	this.parent = parent;
	this.outdir = outdir;
	this.tasks = [];
	this.name = null;
	this.title = "";
	this.id = null;
	this.pilotid = null;
	this.submit = null;
	this.status = null;
	this.check = null;
	this.error = null;
	this.output = null;
	this.expand = false;		// by default no expand the tasklist
	this.checked = false;
}

uw_job.prototype.checkFinish = function(){
	var states = ["finished", "aborted"];
	for (var i in states){
		if (this.status == states[i]) return true;
	}
	return false;
}



function uw_joblist(tdom){
//  tdom - joblist table DOM element 
	var l = tdom;
	if(typeof tdom == "string"){
		 l = document.getElementById(tdom);
		this.tdom = l;
		this.tbody = this.tdom.tBodies[0];
		this.tfoot = this.tdom.tFoot;
	}
	this.jobs = [];
}

uw_joblist.prototype.clear = function () {
	this.jobs.length = 0;
}

uw_joblist.prototype.fill = function (res) {
// res - hash with results from Ajax
	var j,k;
	var ex={};

// save the expand info
	for (j=0; j<this.jobs.length; j++){
		ex[this.jobs[j].id] = this.jobs[j].expand;
	}
// clear the list
	this.clear();

	for (j=0; j<res["jobCnt"]; j++){ 
		this.addjob(res, "job", j);
		if (ex[this.jobs[j].id]) this.jobs[j].expand = true;
		if (res["jobTaskCnt" + eval(j+1)]){
			for (k=0;k<res["jobTaskCnt" + eval(j+1)];k++){
				this.addjob(res, "task", j, k);
			}
		}
	}
	return true;
}
uw_joblist.prototype.addjob = function(res, type, jobn, taskn) {
	var job;
	var pref;
	var postf = jobn +1;
	switch (type){
	case "job":
		pref = "job";
		job = new uw_job(null, "job", "files/");
		this.jobs.push(job);
		job.error = (res["cmdStatus" + postf] ==0)?null: res["retNative" + postf];
		break;
	case "task":
		pref = "task";
		postf += "_" + eval(taskn+1);
		job = new uw_job(this.jobs[jobn], "task",  "files/");
		this.jobs[jobn].tasks.push(job);
		break;
	}
	job.name = res[pref + "Name" + postf];
	job.title = res[pref + "Title" + postf];
	job.id = res[pref + "ID" + postf];
	job.pilotid = res[pref + "PilotID" + postf];
	job.submit = res[pref + "Time" + postf];
	job.status = res[pref + "Status" + postf];
	job.check = res[pref + "StatusTime" + postf];
//	if (type == "job" && job.checkFinish()) job.output = job.outdir;
	return true;
}

uw_joblist.prototype.deljob = function(jobn) {
	return this.jobs.splice(jobn,1);
}

uw_joblist.prototype.clearTable = function () {
	while(this.tbody.rows.length > 0){
		this.tbody.deleteRow(0);
	}
	return true;
}

uw_joblist.prototype.redrawTable = function () {
	this.clearTable();
	this.drawTable();
	this.buttonsState();
	return false;
}

uw_joblist.prototype.addrowTable = function (type, j, t) {
	var oRow, oCell1, oCell2, oCell3,oCheckBox, oImg, oLink;
	var el,d;
	var self = this;
	el = (type == "job") ? this.jobs[j] : this.jobs[j].tasks[t];
	oRow = this.tbody.insertRow(-1);
	oCell = oRow.insertCell(-1);
	oCheckBox = document.createElement("INPUT");

	oCell1 = oRow.insertCell(-1);
	oCell2 = oRow.insertCell(-1);
	oCell3 = oRow.insertCell(-1);
	if (type == "job"){
		oCheckBox.type="checkbox";
    		oCheckBox.id = el.id;
		oCheckBox.onclick = function(){update_state(self, el)}
        	oCell.appendChild(oCheckBox);
// IE bug - set checkbox state after appendChild!!
		oCheckBox.checked = el.checked;

		oLink = document.createElement("A");
		oLink.onclick = function() {change_tasks_list(self,j)};
		oImg = document.createElement("IMG");
		oImg.src = (el.expand) ? "img/minus_jdl.gif" : "img/plus_jdl.gif";
		oLink.appendChild(oImg);
		oCell1.appendChild(oLink);

		oLink = document.createElement("A");
		oLink.onclick = function() {get_job_info(el)};
		oImg = document.createElement("IMG");
		oImg.src = "img/info_16.png";
		oLink.appendChild(oImg);
		oCell2.appendChild(oLink);

		oCell3.innerHTML = htmlspecialchars(el.name);

	} else {
		oRow.className = "task";

		oCheckBox.type="hidden";
		oCheckBox.id = el.title;
		oCell.appendChild(oCheckBox);
oCell1.innerHTML = "&nbsp;";
        
		oLink = document.createElement("A");
		oLink.onclick = function() {get_task_info(el.parent, el)};
		oImg = document.createElement("IMG");
		oImg.src = "img/info_16.png";
		oLink.appendChild(oImg);
		oCell2.appendChild(oLink);
oCell3.innerHTML = "&nbsp;";
	}
	oCell = oRow.insertCell(-1);
	oCell.innerHTML = (el.title)?el.title:"";
	oCell = oRow.insertCell(-1);
	d = new Date(el.submit * 1000);
	oCell.innerHTML =(el.submit)?d.toLocaleString():"";
	oCell = oRow.insertCell(-1);
	oCell.innerHTML = el.status;
	oCell = oRow.insertCell(-1);
	d = new Date(el.check * 1000);
	oCell.innerHTML = (el.check)?d.toLocaleString():"";

	oCell = oRow.insertCell(-1);
	oLink = document.createElement("A");
	oImg = document.createElement("IMG");
	if (el.error){
		oLink.onclick = function() {show_error(el.error)};
		oImg.src = "img/burst.gif";
    	} else {
		oImg.src = "img/blank.gif";
	}
	oLink.appendChild(oImg);
	oCell.appendChild(oLink);

	oCell = oRow.insertCell(-1);
	if (el.output){
		oLink = document.createElement("A");
		oLink.onclick = function() {file_open_download(el.output)};
		oImg = document.createElement("IMG");
		oImg.src = "img/index.gif";
		oLink.appendChild(oImg);
		oCell.appendChild(oLink);
	}	

	return true;
}

uw_joblist.prototype.drawTable = function () {
	var c_jobs=0;
	var c_tasks=0;
this.sort("R");
	var j = this.jobs.length;
	for (var i=0; i<j; i++){ 
		if (this.jobs[i]){
			this.addrowTable("job",i);
			c_jobs++;
this.jobs[i].sort("n");			
			for (var k=0; k<this.jobs[i].tasks.length; k++){ 
				if (this.jobs[i].tasks[k]){
					c_tasks++;
					if (this.jobs[i].expand) this.addrowTable("task",i,k);
				}
			}
		}
	}
	this.tfoot.firstChild.firstChild.innerHTML = "Всего " +c_jobs +wordForms(c_jobs,jobsWordForms) + " и " + c_tasks + wordForms(c_tasks,tasksWordForms) ;
	return true;
}

uw_joblist.prototype.delrowTable = function (res) {
	var postf;
	var j = this.jobs.length;
	for (var i=j-1; i>=0; i--){ 
		if (this.jobs[i]){
			for (var k=0; k<res["jobCnt"]; k++) {
				postf = k+1;
				if (this.jobs[i].id == res["jobID" + postf])  {
					if (res["cmdStatus" + postf] && (res["cmdStatus" + postf] != 0)){
						this.jobs[i].error = res["retNative" + postf];
					} else {
						this.deljob(i);
					}
					break;
				}
			}
		}
	}
	this.redrawTable();
	return true;
}

uw_joblist.prototype.updstatusTable = function(res) {
	var pref, postf;
	var j = this.jobs.length;
	for (var i=j-1; i>=0; i--){ 
		for (var k=0; k<res["jobCnt"]; k++) {
			if (this.jobs[i].id == res["jobID" + eval(k+1)])  {
				pref = "job";
				postf = k+1;
				if (res["cmdStatus" + postf]  && (res["cmdStatus" + postf] != 0)){
					this.jobs[i].error = res["retNative" + postf];
				} else {
					this.jobs[i].error = null;
					this.jobs[i].status = res[pref +"Status" + postf];
					this.jobs[i].check = res[pref +"StatusTime" + postf];
// это надо уточнить!!!
					for (var t=0; t<this.jobs[i].tasks.length; t++){ 
						pref = "task";
						postf = eval(k+1) + "_" + eval(t+1);
						this.jobs[i].tasks[t].id = res[pref +"ID" + postf];
						this.jobs[i].tasks[t].status = res[pref +"Status" + postf];
						this.jobs[i].tasks[t].check = res[pref +"StatusTime" + postf];
					}
                                        			}
				break;
			}
		}
	}
	this.redrawTable();
	return true;
}


uw_joblist.prototype.getoutputTable = function(res) {
	var postf;
	var j = this.jobs.length;
	for (var i=j-1; i>=0; i--){ 
		for (var k=0; k<res["jobCnt"]; k++) {
			postf = k+1;
			if (this.jobs[i].id == res["jobID" + postf])  {
				if (res["cmdStatus" + postf]  && (res["cmdStatus" + postf] != 0)){
					this.jobs[i].error = res["retNative" + postf];
				} else {
					this.jobs[i].error = null;
					this.jobs[i].output = res["outputDir" + postf];
				}
				break;
			}
		}
	}
	this.redrawTable();
	return true;
}

uw_joblist.prototype.cancelTable = function(res) {
	var pref = "job"; 
	var postf;
	var j = this.jobs.length;
	for (var i=j-1; i>=0; i--){ 
		for (var k=0; k<res["jobCnt"]; k++) {
			postf = k+1;
			if (this.jobs[i].id == res["jobID" + postf])  {
				if (res["cmdStatus" + postf]  && (res["cmdStatus" + postf] != 0)){
					this.jobs[i].error = res["retNative" + postf];
				} else {
					this.jobs[i].error = null;
					this.jobs[i].status = res[pref +"Status" + postf];
					this.jobs[i].check = res[pref +"StatusTime" + postf];
				}
				break;
			}
		}
	}
	this.redrawTable();
	return true;
}

uw_joblist.prototype.buttonsState = function(){
	var sms_selected = 0;
	for (var k=0; k< this.jobs.length; k++){
		if (this.jobs[k].checked){
			sms_selected = 1;
			break;
		}
	}
	var btns = getElementsByClassName("list_button", "input", this.tdom.parentNode);
	for (var i = 0, j = btns.length; i < j; i++) {
		btns[i].disabled = !sms_selected;
	}
}

function comp(a, b, obj) {
        var res = 0;
        for(var i = 0; i < obj.order.length; i++) {
                switch(obj.order.substr(i, 1)) {
                        case "n":
                                res = a.name > b.name ? 1 : (a.name < b.name ? -1 : 0);
                                break;
                        case "N":
                                res = a.name > b.name ? -1 : (a.name < b.name ? 1 : 0);
                                break;
                        case "r":
                                res = a.submit > b.submit ? 1 : (a.submit < b.submit ? -1 : 0);
                                break;
                        case "R":
                                res = a.submit > b.submit ? -1 : (a.submit < b.submit ? 1 : 0);
                                break;
                        case "s":
                                res = a.status > b.status ? 1 : (a.status < b.status ? -1 : 0);
                                break;
                        case "S":
                                res = a.status > b.status ? -1 : (a.status < b.status ? 1 : 0);
                                break;
                        case "m":
                                res = a.check > b.check ? 1 : (a.check < b.check ? -1 : 0);
                                break;
                        case "M":
                                res = a.check > b.check ? -1 : (a.check < b.check ? 1 : 0);
                                break;
                }
                if(res) break;
        }
        return res;
}

uw_joblist.prototype.cmp = comp;

uw_job.prototype.cmp = comp;

uw_joblist.prototype.sort = function(ord) {
        if(ord) this.order = ord;
        var obj = this;
        this.jobs.sort(function(a, b) {return obj.cmp(a, b, obj)});
        return true;
}


uw_job.prototype.sort = function(ord) {
        if(ord) this.order = ord;
        var obj = this;
        this.tasks.sort(function(a, b) {return obj.cmp(a, b, obj)});
        return true;
}


function change_tasks_list(joblist, jobn){
	joblist.jobs[jobn].expand = !joblist.jobs[jobn].expand;
	joblist.redrawTable();
	return true;
}

function update_state(joblist, el){
	el.checked = !el.checked;
	joblist.buttonsState();
}

function show_error(errmsg){
	return alert(htmlspecialchars(errmsg));
}

function uw_site(name) {
	this.canRun = false;
	this.isExpand = false;
	this.hasInfo = false;
	this.info = {};
	this.tasks = [];
	this.name = name;

}
function uw_sitelist(dv){
//  dv - parent div
	this.div = document.getElementById(dv);
	this.tdom = null;
	this.head = null;
	this.body = null;
	this.foot = null;
	this.sites = [];
	this.tasks = [];
	this.countTasks = 0;
}


uw_sitelist.prototype.create = function (res) {
// res - hash with results from Ajax
        var j,i;

// remove the old content
	this.sites.length = 0;
	this.tasks.length = 0;
	this.countTasks = 0;

	var k = res["taskCnt"];
// create internal arrays with the results of the request
	if (k != 0){
		var tasks = new Array;
		var sites = new Array;
		var site_list = new Array;
		for ( i=0; i<k; i++){
			tasks[i] = res["taskName_"+ eval(i+1)];
			sites[tasks[i]] = new Array;
			j = 1;
			while ( typeof res["site_" + eval(i+1) + "_" + j] == 'string'){
				var str = res["site_" + eval(i+1) + "_" + j];
				sites[tasks[i]][j-1] =  str;
				if (inArr(site_list,str) == -1) site_list.push(str);
				j++;
			}
		}
// sort site list
		site_list.sort();
// populate the object fields
		this.countTasks = k;
		this.tasks = tasks;
		for ( j=0; j<site_list.length; j++){
			this.sites[j] = new uw_site(site_list[j]);
			var cnt=0;
			for ( i=0; i<k; i++){
				this.sites[j].tasks[i] = (inArr(sites[tasks[i]],site_list[j]) != -1 )? true : false;
				if (this.sites[j].tasks[i]) cnt++;
			}
			if (cnt == k) this.sites[j].canRun = true;
		}
	}
	this.drawTable();
        return true;
}

uw_sitelist.prototype.addinfo = function (res) {
	var ret = false;
	for (var j=0; j<this.sites.length; j++){
		if (this.sites[j].name != res["siteName1"]) continue; 
	        for (var i=1; i<=res["siteParCount1"]; i++){
			this.sites[j].info[res["site_1_par_" + i]] = res["site_1_val_" + i];
		}
		this.sites[j].hasInfo = true;
		ret = true;
		break;
	}
	return ret;
}



uw_sitelist.prototype.drawTable = function () {
var oRow, oCell, oRadio, oLink, oImg;
var i,j;
	var self = this;

// remove the old table
	if (this.tdom) 	this.tdom.parentNode.removeChild(this.tdom);
// create the new table
	this.tdom = document.createElement("TABLE");
	this.head = document.createElement("THEAD");
	this.body = document.createElement("TBODY");
	this.foot = document.createElement("TFOOT");
// Insert the created elements into oTable.
	this.tdom.appendChild(this.head);
	this.tdom.appendChild(this.body);
	this.tdom.appendChild(this.foot);
// Set the table's border width and colors
	this.tdom.className="sort-table";
	this.tdom.style.cellspacing = "0";

// Insert a row into the header.
	oRow = this.head.insertRow(-1);
// Create and insert cells into the header row.

	oCell = oRow.insertCell(-1);
	oCell.style.width = "0";
	oCell.innerHTML = "&nbsp;";

	oRow.insertCell(-1).innerHTML = "&nbsp;";
	oRow.insertCell(-1).innerHTML = "Название узла";
	oRow.insertCell(-1).innerHTML = "Имя очереди";
	oRow.insertCell(-1).innerHTML = "Планировщик";
	for ( i=0; i<this.countTasks; i++){
		oRow.insertCell(-1).innerHTML = this.tasks[i];
	}
	for ( i=0; i<this.sites.length; i++){
// add site row
		oRow = this.body.insertRow(-1);
		oRow.className = "task";

// insert select radio-button for run
		oCell = oRow.insertCell(-1);
		if (this.sites[i].canRun){
			oRadio = createNamedElement("INPUT","sites");
			oRadio.type="radio";
			oRadio.value = 'false';
			oRadio.id = this.sites[i].name;
			oCell.appendChild(oRadio);
		} else {
			oCell.innerHTML = "&nbsp;"
		}
// insert expand sign
		oCell = oRow.insertCell(-1);
		oLink = document.createElement("A");
		oLink.onclick = function(k) {return function(){change_sites_list(self,k)}}(i);
		oImg = document.createElement("IMG");
		oImg.src = (this.sites[i].isExpand) ? "img/minus_jdl.gif" : "img/plus_jdl.gif";
		oLink.appendChild(oImg);
		oCell.appendChild(oLink);
		if (this.sites[i].hasInfo){
			oLink = document.createElement("A");
			oLink.onclick = function(k){return function () {get_site_info(self.sites[k].name)}}(i);
			oImg = document.createElement("IMG");
			oImg.src = "img/refr_jdl.gif";
			oImg.style.marginLeft = "3px";
			oImg.alt = "Обновить информацию о ресурсе";
			oImg.title = "Обновить информацию о ресурсе";
			oLink.appendChild(oImg);
			oCell.appendChild(oLink);
		}
// split site name
		var fields = this.sites[i].name.split('/');
// insert host name
		oRow.insertCell(-1).innerHTML = fields[0];
// insert scheduler
		oRow.insertCell(-1).innerHTML = fields[2];
// insert queue name
		oCell = oRow.insertCell(-1);
		oCell.style.width = "50%";
		oCell.innerHTML = fields[1];
// insert plus/minus sign
		for (j=0; j<this.tasks.length; j++){
			oRow.insertCell(-1).innerHTML = (this.sites[i].tasks[j]) ? "+" : "&ndash;";
		}
// check and insert site info rows
		if (this.sites[i].isExpand  && this.sites[i].hasInfo){
			for(var key in this.sites[i].info) {
				oRow = this.body.insertRow(-1);
				oCell = oRow.insertCell(-1);
				oCell.colSpan = "2";
				oCell.innerHTML = "&nbsp;";
				oRow.insertCell(-1).innerHTML = key;
				oCell = oRow.insertCell(-1);
				oCell.colSpan = "2";
				oCell.innerHTML = this.sites[i].info[key];
				oCell = oRow.insertCell(-1);
				oCell.innerHTML = "&nbsp;";
				oCell.colSpan = eval(this.tasks.length);
			}
		} 
	}
// Insert text to footer
	oRow = this.foot.insertRow(-1);
	oCell = oRow.insertCell(-1);
	oCell.colSpan = eval(this.tasks.length+5);
	oCell.style.backgroundColor = "lightskyblue";
	oCell.innerHTML = "Всего " + this.sites.length + " различных " + wordForms(this.sites.length, sitesWordForms) + " для задания из " + this.tasks.length + " задач";

// append the table
	this.div.appendChild(this.tdom);

	return true;
}



function change_sites_list(sitelist,j) {
	sitelist.sites[j].isExpand = !sitelist.sites[j].isExpand;
	(sitelist.sites[j].hasInfo) ? sitelist.drawTable() : get_site_info(sitelist.sites[j].name);
	return true;
}

function inArr(arr, str){
	for(var i=0; i<arr.length; i++){
		if(arr[i]==str){
			return i;
		}
	}
	return -1;
}
