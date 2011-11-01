var _uw_mutex = new Array();
function _uw_mouseover(e, obj, f) {
    _uw_mutex[obj]++;
    if(_uw_mutex[obj] > 1) {
	_uw_mutex[obj]--;
	return true;
    }
    var tgt;
    if(! e) {
	e = event;
	e.returnValue = false;
	tgt = e.srcElement;
    } else {
	e.preventDefault();
	tgt = e.target;
    }
    if(f) {
        obj.mover = tgt;
    } else {
	obj.mover = null;
    }
    obj.refresh(true);
    _uw_mutex[obj]--;
    return false;
}
function _uw_mouselock(e, obj, f) {
    _uw_mutex[obj]++;
    if(_uw_mutex[obj] > 1) {
	_uw_mutex[obj]--;
	return true;
    }
    if(obj.nolock) {
	_uw_mutex[obj]--;
	obj.nolock = false;
	return true;
    }
    if(! e) {
	e = event;
	e.returnValue = false;
	e.cancelBubble = true;
    } else {
	e.preventDefault();
	e.stopPropagation();
    }
    if(f) {
        obj.lx = e.clientX;
	obj.ly = e.clientY;
    } else {
	obj.resize = false;
    }
    obj.mlock = f;
    obj.refresh(true);
    _uw_mutex[obj]--;
    return false;
}
function _uw_mousemove(e, obj) {
    _uw_mutex[obj]++;
    if(_uw_mutex[obj] > 1) {
	_uw_mutex[obj]--;
	return true;
    }
    if(! obj.mlock) {
	_uw_mutex[obj]--;
	return true;
    }
    if(! e) {
	e = event;
	e.returnValue = false;
    } else {
	e.preventDefault();
    }
    if(obj.resize) {
	obj.w += e.clientX - obj.lx;
	obj.h += e.clientY - obj.ly;
	if(obj.w < obj.minw) obj.w = obj.minw;
	if(obj.h < obj.minh) obj.h = obj.minh;
	obj.lx = e.clientX;
	obj.ly = e.clientY;
    } else {
	obj.x += e.clientX - obj.lx;
	obj.y += e.clientY - obj.ly;
	obj.lx = e.clientX;
	obj.ly = e.clientY;
    }
    obj.refresh(false);
    _uw_mutex[obj]--;
    return false;
}
function _uw_mousepos(e, dom) {
    var x = 0;
    var y = 0;
    if(!e) e = event;
    if(e.pageX || e.pageY) {
	x = e.pageX;
	y = e.pageY;
    } else if(e.clientX || e.clientY) {
	x = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
	y = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }
    while(dom) {
	x -= dom.offsetLeft - dom.scrollLeft;
	y -= dom.offsetTop - dom.scrollTop;
	dom = dom.offsetParent;
    }
    return [x, y];
}
function uw_dagedit(pdom, cname) {
    _uw_mutex[this] = 0;
    this.name = "dagedit";
    this.changed = false;
    this.dom = null;
    this.json = null;
    this.nodes = null;
    this.links = null;
    this.snode = null;
    this.alink = null;
    this.toolbar = null;
    if(cname) {
        this.cname = cname;
    } else {
        this.cname = this.name;
    }
    var tdom = document.createElement("div");
    var tdomm = document.createElement("div");
    tdom.className = this.cname;
    tdomm.className = this.cname + "-main";
    var obj = this;
    tdomm.onmousemove = function(e) {
	if(obj.snode) _uw_mousemove(e, obj.snode);
	if(obj.snode && (obj.snode.mover || obj.snode.mlock)) return false;
        if(! e) e = event;
	var md = 10;
	var ml = null;
	var l = obj.links;
	while(l) {
	    if(l.src && l.dst) {
		var pos = _uw_mousepos(e, obj.dom.firstChild);
		var d = l.dist(pos[0], pos[1]);
		if(d < md) {
		    md = d;
		    ml = l;
		}
	    }
	    l = l.next;
	}
	if(obj.alink && obj.alink != ml) {
	    obj.alink.mover = null;
	    obj.alink.refresh(false);
	    obj.alink = null;
	}
	if(ml != obj.alink) {
	    ml.mover = ml.dom;
	    ml.refresh(false);
	    obj.alink = ml;
	}
	return false;
    }
    tdomm.onmouseup = function(e) {
	if(obj.alink && confirm("Удалить зависимость между узлами " + obj.alink.src.title + " и " + obj.alink.dst.title + " ?")) obj.dellink(obj.alink);
	return false;
    }
    tdom.appendChild(tdomm);
    this.dom = tdom;
    if(pdom) pdom.appendChild(tdom);
    var toolbar = new uw_dagtoolbar(this);
    this.dom.appendChild(toolbar.dom);
    this.toolbar = toolbar;
}
uw_dagedit.prototype.addnode = function() {
    var node = new uw_dagnode(this);
    node.prev = null;
    node.next = this.nodes;
    if(this.nodes) {
	node.idx = this.nodes.idx + 1;
	this.nodes.prev = node;
    }
    this.nodes = node;
    node.x = 50 + 20 * node.idx;
    node.y = 50 + 20 * node.idx;
    node.w = 110;
    node.h = 60;
    node.minw = 110;
    node.minh = 60;
    this.dom.firstChild.appendChild(node.dom);
    node.refresh(false);
    this.changed = true;
    return node;
}
uw_dagedit.prototype.delnode = function(node) {
    if(! this.nodes || ! node) return false;
    if(this.snode == node) this.snode = null;
    if(node == this.nodes) this.nodes = node.next;
    if(node.next) node.next.prev = node.prev;
    if(node.prev) node.prev.next = node.next;
    this.dom.firstChild.removeChild(node.dom);
    var l = this.links;
    while(l) {
	if(l.src == node || l.dst == node) this.dellink(l);
	l = l.next;
    }
    this.changed = true;
    return true;
}
uw_dagedit.prototype.addlink = function(src, dst) {
    if(! src) return false;
    var link = new uw_daglink(this);
    link.src = src;
    src.nd++;
    src.refresh(false);
    if(dst) {
	link.dst = dst;
	dst.nu++;
	dst.refresh(false);
    }
    link.prev = null;
    link.next = this.links;
    if(this.links) this.links.prev = link;
    this.links = link;
    this.dom.firstChild.appendChild(link.dom);
    link.refresh(false);
    this.changed = true;
    return link;
}
uw_dagedit.prototype.dellink = function(link) {
    if(! this.links || ! link) return false;
    if(link.src) {
	link.src.nd--;
	link.src.refresh(false);
    }
    if(link.dst) {
	link.dst.nu--;
	link.dst.refresh(false);
    }
    if(link == this.links) this.links = link.next;
    if(link.next) link.next.prev = link.prev;
    if(link.prev) link.prev.next = link.next;
    if(link == this.alink) this.alink = null;
    this.dom.firstChild.removeChild(link.dom);
    this.changed = true;
    return true;
}
uw_dagedit.prototype.clonenode = function(node) {
    if(! node) return false;
    var clone = this.addnode();
    clone.x = 50 + node.x;
    clone.y = 50 + node.y;
    clone.w = node.w;
    clone.h = node.h;
    clone.title = node.title;
    clone.file = node.file;
    clone.task = node.task;
    var l = this.links;
    while(l) {
	if(l.src == node && l.dst) this.addlink(clone, l.dst);
	if(l.dst == node && l.src) this.addlink(l.src, clone);
	l = l.next;
    }
    clone.refresh(false);
    return clone;
}
uw_dagedit.prototype.dfs = function(n, v, i) {
    if(v[n.idx]) return n.idx == i;
    v[i] = true;
    var l = this.links;
    while(l) {
	if(l.src == n && l.dst && this.dfs(l.dst, v, i)) return true;
	l = l.next;
    }
    return false;
}
uw_dagedit.prototype.validatelink = function(src, dst, f) {
    if(src == dst) {
	if(f) alert("Данная зависимость приведёт к появлению недопустимой петли в графе");
	return false;
    }
    var l = this.links;
    while(l) {
	if(l.src == src && l.dst == dst) {
    	    if(f) alert("Данная зависимость уже существует");
    	    return false;
	}
	l = l.next;
    }
    var v = new Array();
    if(this.dfs(dst, v, src.idx)) {
	if(f) alert("Данная зависимость приведёт к появлению недопустимого цикла в графе");
	return false;
    }
    return true;
}
uw_dagedit.prototype.text = function() {
    if(! this.json) this.json = {};
    this.json.version = 2;
    this.json.tasks = new Array();
    var n = this.nodes;
    while(n) {
	var tmp = {};
	tmp.id = "n" + n.idx;
	tmp.description = n.title;
	if(n.task) tmp.definition = n.task; else tmp.filename = n.file;
	if(! tmp.meta) tmp.meta = {};
	tmp.meta.x = n.x;
	tmp.meta.y = n.y;
	tmp.meta.w = n.w;
	tmp.meta.h = n.h;
        var l = this.links;
	while(l) {
	    if(l.src && l.dst && l.src == n) {
		if(! tmp.children) tmp.children = new Array();
		tmp.children.push("n" + l.dst.idx);
	    }
	    l = l.next;
	}
	this.json.tasks.push(tmp);
	n = n.next;
    }
    try {
        return JSON.stringify(this.json, null, '\t');
    } catch(e) {
	return false;
    }
}
uw_dagedit.prototype.load = function(t) {
    this.clear();
    var map = new Array();
    try {
	this.json = JSON.parse(t);
    } catch(e) {
	return false;
    }
    for(var i in this.json.tasks) {
        var node = this.addnode();
        map[this.json.tasks[i].id] = node;
	if(this.json.tasks[i].description) node.title = this.json.tasks[i].description;
	if(this.json.tasks[i].filename) node.file = this.json.tasks[i].filename; else
	    if(this.json.tasks[i].definition) node.task = this.json.tasks[i].definition;
	if(this.json.tasks[i].meta) {
	    if(typeof this.json.tasks[i].meta.x == "number") node.x = Math.abs(Math.round(this.json.tasks[i].meta.x));
	    if(typeof this.json.tasks[i].meta.y == "number") node.y = Math.abs(Math.round(this.json.tasks[i].meta.y));
	    if(typeof this.json.tasks[i].meta.w == "number") node.w = Math.abs(Math.round(this.json.tasks[i].meta.w));
	    if(typeof this.json.tasks[i].meta.h == "number") node.h = Math.abs(Math.round(this.json.tasks[i].meta.h));
	}
	node.refresh(false);
    }
    for(var i in this.json.tasks)
	for(var c in this.json.tasks[i].children) {
	    if(map[this.json.tasks[i].children[c]] && this.validatelink(map[this.json.tasks[i].id], map[this.json.tasks[i].children[c]], false)) {
		this.addlink(map[this.json.tasks[i].id], map[this.json.tasks[i].children[c]]);
		map[this.json.tasks[i].id].refresh(false);
		map[this.json.tasks[i].children[c]].refresh(false);
	    }
	}
    return true;
}
uw_dagedit.prototype.clear = function() {
    this.snode = null;
    var l = this.links;
    while(l) {
	this.dom.firstChild.removeChild(l.dom);
	l = l.next;
    }
    this.links = null;
    var n = this.nodes;
    while(n) {
	this.dom.firstChild.removeChild(n.dom);
	n = n.next;
    }
    this.nodes = null;
    this.json = null;
    return true;
}
uw_dagedit.prototype.notify = function(obj) {
    if(obj.name == "dagnode") {
	if(obj.select) {
            if(this.snode && this.snode != obj) {
		this.snode.mlock = false;
		this.snode.select = false;
		this.snode.refresh(false);
	    }
	    this.snode = obj;
	}
	if(this.links && ! this.links.dst) {
	    if(obj.mover) obj.dom.className = obj.cname + (this.validatelink(this.links.src, obj) ? "-linkable" : "-nonlinkable");
	    if(obj.mlock) {
		obj.mlock = false;
		if(! this.validatelink(this.links.src, obj, true)) {
		    this.dellink(this.links)
		} else {
		    this.links.dst = obj;
		    obj.nu++;
		}
		obj.refresh(false);
	    }
	}
	var l = this.links;
	while(l) {
	    if(l.src == obj || l.dst == obj) l.refresh(false);
	    l = l.next;
	}
    }
    return true;
}
function uw_dagtoolbar(parent, cname) {
    _uw_mutex[this] = 0;
    this.name = "dagtoolbar";
    this.parent = parent;
    this.dom = null;
    this.tools = null;
    this.mover = null;

    if(cname) {
        this.cname = cname;
    } else {
        this.cname = this.name;
    }
    var tdom = document.createElement("div");
    tdom.className = this.cname;
    this.dom = tdom;
}

// add for filetoolbar
uw_dagtoolbar.prototype.addtext = function(id) {
    if(! this.dom) return false;
    var tdom = document.createElement("input");
    tdom.type = "text";
    tdom.className = this.cname + "-text";
    tdom.id = id;
    tdom.size = 70;
    tdom.disabled = "disabled";
    this.dom.appendChild(tdom);
    return true;        
}

uw_dagtoolbar.prototype.addtool = function(name, icon, action) {
    if(! this.dom) return false;
    var tdom = document.createElement("img");
    tdom.className = this.cname + "-icon";
    tdom.alt = name;
    tdom.title = name;
    tdom.src = icon;
    var obj = this;
    tdom.onmouseover = function(e) {_uw_mouseover(e, obj, true)};
    tdom.onmouseout = function(e) {_uw_mouseover(e, obj, false)};
    tdom.onclick = function(e) {eval(action)};
    this.dom.appendChild(tdom);
    return true;
}
uw_dagtoolbar.prototype.refresh = function(flag) {
    if(! this.dom) return false;
    for(var i in this.dom.childNodes) {

// check for text input
	if(this.dom.childNodes[i].className == this.cname + "-text") continue;

	if(this.mover == this.dom.childNodes[i]) {
	    this.dom.childNodes[i].className = this.cname + "-icon-selected";
	} else {
	    this.dom.childNodes[i].className = this.cname + "-icon";
	}
    }
    return true;
}
function uw_dagnode(parent, cname) {
    _uw_mutex[this] = 0;
    this.name = "dagnode";
    this.parent = parent;
    this.dom = null;
    this.title = "Нет имени";
    this.file = "Нет файла";
    this.task = null;
    this.x = 0;
    this.y = 0;
    this.w = 0;
    this.h = 0;
    this.minw = 0;
    this.minh = 0;
    this.idx = 0;
    this.nu = 0;
    this.nd = 0;
    this.next = null;
    this.prev = null;
    this.nolock = false;
    this.select = false;
    this.mlock = false;
    this.resize = false;
    this.mover = null;
    this.lx = 0;
    this.ly = 0;

    if(cname) {
        this.cname = cname;
    } else {
        this.cname = this.name;
    }
    var tdom = document.createElement("div");
    var tdomt = document.createElement("div");
    var tdomt1 = document.createElement("textarea");
    var tdomt2 = document.createElement("div");
    var tdomt2b = document.createElement("button");
    var tdoms = document.createElement("div");
    var tdomi = document.createElement("img");
    tdom.className = this.cname;
    tdomt.className = this.cname + "-main";
    tdomt1.className = this.cname + "-name";
    tdomt2.className = this.cname + "-file";
    tdomt2b.className = this.cname + "-fileselect";
    tdoms.className = this.cname + "-status";
    tdomi.className = this.cname + "-corner";
    tdomi.src = "img/dag_resize_corner.png"; //FIXME: corner icon should be specified via CSS
    tdomt2b.innerHTML = "&hellip;";
    tdomt2.appendChild(document.createTextNode(""));
    tdomt2.appendChild(tdomt2b);
    tdomt.appendChild(tdomt1);
    tdomt.appendChild(tdomt2);
    tdom.appendChild(tdomt);
    tdom.appendChild(tdoms);
    tdom.appendChild(tdomi);
    var obj = this;
    tdom.onmousedown = function(e) {_uw_mouselock(e, obj, true)}
    tdom.onmouseup = function(e) {_uw_mouselock(e, obj, false)};
    tdom.onmouseout = function(e) {_uw_mouseover(e, obj, false)};
    tdom.onmouseover = function(e) {_uw_mouseover(e, obj, true)};
    tdomt1.onmousedown = function(e) {obj.nolock = true; obj.select = true; this.focus()};
    tdomt1.onmouseup = function(e) {obj.nolock = true; obj.mlock = false};
    tdomt1.onkeyup = function(e) {var title = this.value.replace(/\n/g, " "); if(obj.title != title && obj.parent) obj.parent.changed = true; obj.title = title};
    tdomt1.onchange = function(e) {var title = this.value.replace(/\n/g, " "); if(obj.title != title && obj.parent) obj.parent.changed = true; obj.title = title};
    tdomt2b.onclick = function(e) {file_select({init_path: "jdl-" + ada + "/", path: obj.file.substring(0, obj.file.lastIndexOf("/")), cb: function(file) {if(obj.parent) obj.parent.changed = true; obj.file = file; obj.refresh(false)}, btntext: "Выбрать"})};
    tdomi.onmousedown = function(e) {obj.resize = true};
    this.dom = tdom;
}
uw_dagnode.prototype.refresh = function(flag) {
    if(! this.dom) return false;
    if(this.mlock) this.select = true;
    this.dom.firstChild.childNodes[0].value = this.title;
    if(this.task) {
        this.dom.firstChild.childNodes[1].childNodes[0].nodeValue = "\u2014 задача \u2014";
        this.dom.firstChild.childNodes[1].childNodes[1].disabled = true;
    } else {
        this.dom.firstChild.childNodes[1].childNodes[0].nodeValue = this.file;
        this.dom.firstChild.childNodes[1].childNodes[1].disabled = false;
    }
    this.dom.childNodes[1].innerHTML = "&nbsp;&bull;" + this.idx + "&nbsp;&uarr;" + this.nu + "&nbsp;&darr;" + this.nd;
    this.dom.className = this.cname + ((this.mlock || this.mover) ? "-moving" : this.select ? "-selected" : "");
    this.dom.style.left = this.x + "px";
    this.dom.style.top = this.y + "px";
    this.dom.style.width = this.w + "px";
    this.dom.style.height = this.h + "px";
    var fh = ((this.dom.clientHeight - this.dom.lastChild.previousSibling.clientHeight) / 2) + "px";
    this.dom.firstChild.firstChild.style.height = fh;
    this.dom.firstChild.lastChild.style.height = fh;
    if(flag && this.parent) this.parent.notify(this);
    return true;
}
function uw_daglink(parent, cname) {
    _uw_mutex[this] = 0;
    this.name = "daglink";
    this.parent = parent;
    this.mover = null;
    this.mlock = false;
    this.resize = false;
    this.x = 0;
    this.y = 0;
    this.lx = 0;
    this.ly = 0;
    this.prev = null;
    this.next = null;
    this.dom = null;
    this.src = null;
    this.dst = null;
    this.step = 5;
    this.linew = 2;
    this.mind = 20;
    this.arrl = 15;
    this.arra = 30;
    this.x1 = 0;
    this.y1 = 0;
    this.x2 = 0;
    this.y2 = 0;

    if(cname) {
        this.cname = cname;
    } else {
        this.cname = this.name;
    }
    var tdom = document.createElement("div");
    var tdoms = document.createElement("div");
    tdom.className = this.cname;
    tdoms.className = this.cname;
    tdom.appendChild(tdoms);
    this.dom = tdom;
}
uw_daglink.prototype.dist = function(x, y) {
    var eps = 0.0;
    if(((x - this.x1) * (this.x2 - this.x1) + (y - this.y1) * (this.y2 - this.y1)) *
       ((x - this.x2) * (this.x2 - this.x1) + (y - this.y2) * (this.y2 - this.y1)) > -eps) {
	var t = Math.pow(x - this.x1, 2) + Math.pow(y - this.y1, 2);
        var w = Math.pow(x - this.x2, 2) + Math.pow(y - this.y2, 2);
	if(w < t) t = w;
	return Math.sqrt(t);
    } else {
	return Math.sqrt(Math.pow((x - this.x1) * (this.y2 - this.y1) - (y - this.y1) * (this.x2 - this.x1), 2) /
                	(Math.pow(this.x2 - this.x1, 2) + Math.pow(this.y2 - this.y1, 2)));
    }
}
uw_daglink.prototype.refresh = function(flag) {
    if(! this.dom || ! this.src || ! this.dst) return false;
    var x1 = this.src.x + this.src.w / 2;
    var y1 = this.src.y + this.src.h / 2;
    var x2 = this.dst.x + this.dst.w / 2;
    var y2 = this.dst.y + this.dst.h / 2;
    var w = Math.max(Math.abs(x2 - x1), this.mind);
    var h = Math.max(Math.abs(y2 - y1), this.mind);
    var r = this.arrl / Math.sqrt(w * w + h * h);
    var xa = w * r * (x1 < x2 ? -1 : 1);
    var ya = h * r * (y1 < y2 ? -1 : 1);
    if(x1 != this.x1 || y1 != this.y1 || x2 != this.x2 || y2 != this.y2) {
	this.x1 = x1;
	this.y1 = y1;
	this.x2 = x2;
	this.y2 = y2;
        var obj = this;
        var tdom = document.createElement("canvas");
        if(! tdom || ! tdom.getContext) {
    	    var tdom = document.createElement("div");
    	    tdom.className = this.cname;
	    var len = Math.round(Math.sqrt(w * w + h * h));
	    for(var i = 0; i <= len; i += this.step) {
		var dot = document.createElement("div");
		dot.style.left = Math.round(x1 < x2 ? w * i / len : w - w * i / len) + "px";
		dot.style.top = Math.round(y1 < y2 ? h * i / len : h - h * i / len) + "px";
		tdom.appendChild(dot);
    	    }
    	    var ex1 = xa * Math.cos(this.arra * Math.PI / 180) - ya * Math.sin(this.arra * Math.PI / 180) + (w - 1) / 2;
    	    var ey1 = xa * Math.sin(this.arra * Math.PI / 180) + ya * Math.cos(this.arra * Math.PI / 180) + (h - 1) / 2;
    	    var ex2 = xa * Math.cos(-this.arra * Math.PI / 180) - ya * Math.sin(-this.arra * Math.PI / 180) + (w - 1) / 2;
    	    var ey2 = xa * Math.sin(-this.arra * Math.PI / 180) + ya * Math.cos(-this.arra * Math.PI / 180) + (h - 1) / 2;
    	    for(var i = 0; i <= this.arrl; i += this.step) {
    		var dot1 = document.createElement("div");
		dot1.style.left = Math.round((ex1 * i + (w - 1) / 2 * (this.arrl - i)) / this.arrl) + "px";
		dot1.style.top = Math.round((ey1 * i + (h - 1) / 2 * (this.arrl - i)) / this.arrl) + "px";
		var dot2 = document.createElement("div");
		dot2.style.left = Math.round((ex2 * i + (w - 1) / 2 * (this.arrl - i)) / this.arrl) + "px";
		dot2.style.top = Math.round((ey2 * i + (h - 1) / 2 * (this.arrl - i)) / this.arrl) + "px";
		tdom.appendChild(dot1);
		tdom.appendChild(dot2);
    	    }
        } else {
    	    tdom.className = this.cname + "-canvas";
    	    tdom.width = w;
	    tdom.height = h;
    	}    
    	tdom.style.left = Math.min(x1, x2) + "px";
    	tdom.style.top = Math.min(y1, y2) + "px";
    	tdom.style.width = w + "px";
    	tdom.style.height = h + "px";
        this.dom.appendChild(tdom);
        this.dom.removeChild(this.dom.firstChild);
    }
    if(this.dom.lastChild.getContext) {
        var con = this.dom.lastChild.getContext("2d");
        con.lineWidth = this.linew;
        con.strokeStyle = this.mover ? "red" : "black";
        con.clearRect(0, 0, w - 1, h - 1);
        con.moveTo(x1 < x2 ? 0 : w - 1, y1 < y2 ? 0 : h - 1);
        con.lineTo(x1 > x2 ? 0 : w - 1, y1 > y2 ? 0 : h - 1);
        con.moveTo((w - 1) / 2, (h - 1) / 2);
        con.lineTo(Math.round(xa * Math.cos(this.arra * Math.PI / 180) - ya * Math.sin(this.arra * Math.PI / 180) + (w - 1) / 2),
    		   Math.round(xa * Math.sin(this.arra * Math.PI / 180) + ya * Math.cos(this.arra * Math.PI / 180) + (h - 1) / 2));
        con.moveTo((w - 1) / 2, (h - 1) / 2);
        con.lineTo(Math.round(xa * Math.cos(-this.arra * Math.PI / 180) - ya * Math.sin(-this.arra * Math.PI / 180) + (w - 1) / 2),
    		   Math.round(xa * Math.sin(-this.arra * Math.PI / 180) + ya * Math.cos(-this.arra * Math.PI / 180) + (h - 1) / 2));
        con.stroke();
    } else {
	for(var i in this.dom.lastChild.childNodes) {
	    this.dom.lastChild.childNodes[i].className = this.cname + (this.mover ? "-dot-active" : "-dot");
	}
    }
    if(flag && this.parent) this.parent.notify(this);
    return true;
}
function uw_popup(cb, cname) {
    _uw_mutex[this] = 0;
    this.name = "popup";
    this.dom = null;
    this.cb = cb;
    this.x = 100;
    this.y = 100;
    this.w = 300;
    this.h = 180;
    this.minw = 100;
    this.minh = 160;
    this.mlock = false;
    this.resize = false;
    this.mover = null;
    this.lx = 0;
    this.ly = 0;

    if(cname) {
        this.cname = cname;
    } else {
        this.cname = this.name;
    }
    var tdom = document.createElement("div");
    var tdomh = document.createElement("div");
    tdomh.innerHTML = "Сообщение";
    var tdomt = document.createElement("div");
    var tdomm = document.createElement("span");
    var tdoms = document.createElement("table");
    var tdomi = document.createElement("img");
    var tdomc = document.createElement("img");
    var tdomby = document.createElement("img");
    var tdombn = document.createElement("img");
    var tdoma = document.createElement("img");
    tdom.className = this.cname;
    tdomh.className = this.cname + "-title";
    tdomt.className = this.cname + "-main";
    tdomm.className = this.cname + "-content";
    tdoms.className = this.cname + "-buttonbar";
    tdomi.className = this.cname + "-corner";
    tdomc.className = this.cname + "-close";
    tdoma.className = this.cname + "-icon";
    tdomby.className = this.cname + "-button";
    tdomby.title = "Да";
    tdombn.className = this.cname + "-button";
    tdombn.title = "Отмена";
    tdomi.src = "img/dag_resize_corner.png"; //FIXME: corner icon should be specified via CSS
    tdomc.src = "img/close_window.png"; //FIXME: close icon should be specified via CSS
    tdomby.src = "img/button_ok.png"; //FIXME: OK icon should be specified via CSS
    tdombn.src = "img/button_cancel.png"; //FIXME: Cancel icon should be specified via CSS
    tdoma.src = "img/message_icon.png"; //FIXME: message icon should be specified via CSS
    var btnr = tdoms.insertRow(-1);
    btnr.align = "center";
    btnr.insertCell(-1).appendChild(tdomby);
    btnr.insertCell(-1).appendChild(tdombn);
    tdomt.appendChild(tdoma);
    tdomt.appendChild(tdomm);
    tdom.appendChild(tdomh);
    tdom.appendChild(tdomt);
    tdom.appendChild(tdoms);
    tdom.appendChild(tdomi);
    tdom.appendChild(tdomc);
    var obj = this;
    tdom.onmousemove = function(e) {_uw_mousemove(e, obj)}
    tdom.onmouseup = function(e) {_uw_mouselock(e, obj, false)};
    tdom.onmouseout = function(e) {_uw_mouselock(e, obj, false)};
    tdomh.onmousedown = function(e) {_uw_mouselock(e, obj, true)}
    tdomi.onmousedown = function(e) {obj.resize = true; _uw_mouselock(e, obj, true)};
    tdomc.onclick = function(e) {obj.cancel()};
    tdomby.onclick = function(e) {obj.ok()};
    tdombn.onclick = function(e) {obj.cancel()};
    this.dom = tdom;
}
uw_popup.prototype.set_title = function(text) {
    this.dom.childNodes[0].innerHTML = text;
}
uw_popup.prototype.set_text = function(text) {
    this.dom.childNodes[1].childNodes[1].innerHTML = text;
}
uw_popup.prototype.center = function() {
    this.x = (document.documentElement.clientWidth - this.w) / 2;
    this.y = (document.documentElement.clientHeight - this.h) / 2;
    this.refresh(true);
}
uw_popup.prototype.show = function() {
    this.refresh(false);
    document.body.appendChild(this.dom);
}
uw_popup.prototype.hide = function() {
    document.body.removeChild(this.dom);
}
uw_popup.prototype.ok = function() {
    alert("OK");
    this.hide();
    if(this.cb) this.cb(true);
}
uw_popup.prototype.cancel = function() {
    alert("Cancel");
    this.hide();
    if(this.cb) this.cb(false);
}
uw_popup.prototype.refresh = function(flag) {
    if(! this.dom) return false;
    this.dom.style.left = this.x + "px";
    this.dom.style.top = this.y + "px";
    this.dom.style.width = this.w + "px";
    this.dom.style.height = this.h + "px";
    return true;
}
