function showPopup(el, text){
	el.onmousemove = positiontip;
	var popup = document.getElementById('popup');
	popup.style.display = 'block';
	popup.innerHTML = text;
}

function hidePopup(el){
	document.getElementById('popup').style.display = 'none';
	el.onmousemove = '';
}

function positiontip(e) {
	var popup = document.getElementById('popup');
	var offsetfromcursorY = 15 // y offset of tooltip
	var ietruebody = (document.compatMode && document.compatMode!="BackCompat") ? document.documentElement : document.body;
	var ie = document.all && !window.opera;
	var ns6 = document.getElementById && !document.all;
	var curX = (ns6) ? e.pageX : event.clientX+ietruebody.scrollLeft;
	var curY = (ns6) ? e.pageY : event.clientY+ietruebody.scrollTop;
	var winwidth = ie ? ietruebody.clientWidth : window.innerWidth-20
	var winheight = ie ? ietruebody.clientHeight : window.innerHeight-20

	var rightedge=ie? winwidth-event.clientX : winwidth-e.clientX;
	var bottomedge=ie? winheight-event.clientY-offsetfromcursorY : winheight-e.clientY-offsetfromcursorY;

	if (rightedge < popup.offsetWidth) popup.style.left=curX-popup.offsetWidth+"px";
	else popup.style.left=curX+15+"px";

	if (bottomedge < popup.offsetHeight) popup.style.top=curY-popup.offsetHeight-offsetfromcursorY+"px"
	else popup.style.top=curY+offsetfromcursorY+15+"px";
}

var gAutoUpdateTimerID;
var gAutoUpdateRemains;

function autoUpdate(interval, indicator){
	
	var textBox = indicator ? $(indicator) : $('<span />');
	
	gAutoUpdateRemains = interval ? interval * 1000 : 30000;
	if (gAutoUpdateTimerID) clearInterval(gAutoUpdateTimerID);
	gAutoUpdateTimerID = setInterval(function(){
		gAutoUpdateRemains -= 1000;
		
		if (!gAutoUpdateRemains){
			textBox.html(LNG.taskSetUpdateUpdating);
			clearInterval(gAutoUpdateTimerID);
			$.get(location.href, function(response){
				$('#main').html(response);
				textBox.html(LNG.taskSetUpdateStr1 + " <b>" + (gAutoUpdateRemains / 1000) + "</b> " + LNG.taskSetUpdateSec);
			});
		} else {
			textBox.html(LNG.taskSetUpdateStr1 + " <b>" + (gAutoUpdateRemains / 1000) + "</b> " + LNG.taskSetUpdateSec);
		}
	}, 1000);
}

function refresh(indicator){
	var textBox = indicator ? $(indicator) : $('<span />');
	if (gAutoUpdateTimerID) clearInterval(gAutoUpdateTimerID);
	textBox.html(LNG.taskSetUpdateUpdating);
	$.get(location.href, function(response){
		$('#main').html(response);
		textBox.html(LNG.taskSetUpdateStr1 + " <b>" + (gAutoUpdateRemains / 1000) + "</b> " + LNG.taskSetUpdateSec);
	});
}

function tabs(el){
	$(el+'>div').hide();
	$(el+'>ul a').removeClass('active');
	if (location.href.indexOf('#') != -1){
		var tab = location.href.substr(location.href.lastIndexOf('/') + 1);
		$('#' + tab).show();
		$(el+'>ul a[href=#' + tab + ']').addClass('active');
	}
	else {
		$(el+'>div:eq(1)').show();
		$(el+'>ul a:first').addClass('active');
	}
	$(el+'>ul a').click(function(){
		$(el+'>div').hide();
		$(this.href.substr(this.href.lastIndexOf('/') + 1)).show();
		$(el+'>ul a').removeClass('active');
		$(this).addClass('active');
	});
}
