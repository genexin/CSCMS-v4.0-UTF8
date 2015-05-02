<!--
document.write("<style type='text/css'>");
document.write(".Cstip_text_div{background-color:#FFF;border:1px solid gray;padding:3px;color:#349045;FONT-FAMILY:Courier New,tahoma,arial;font-size: 12px;}");
document.write("</style>");
var Cstip = new Object();
Cstip.cfg_txt_tag='Cstip';
Cstip.cfg_css='Cstip_text_div';
Cstip.cfg_xoffset=10;
Cstip.cfg_yoffset=20; 
Cstip.cfg_ie_frame_fix=true;
Cstip.cfg_mouse_follow=true;
Cstip._divZIndex = 10000;
Cstip._oBody = null;
Cstip._ifr = null;
Cstip._CstipDiv = null;
Cstip._mousePos = new Object();
Cstip._cssProp = 'display'; Cstip._cssVis = ''; Cstip._cssHid = 'none';
Cstip.isIE = !!(window.attachEvent && !window.opera);
Cstip._init = function(){
	try{
		Cstip._oBody = document.getElementsByTagName("BODY").item(0);
		Cstip._CstipDiv = document.createElement("DIV");
		Cstip._CstipDiv.className = Cstip.cfg_css;
		Cstip._CstipDiv.style.position = "absolute";
		Cstip._CstipDiv.style.zIndex = Cstip._divZIndex;
		Cstip._oBody.appendChild(Cstip._CstipDiv);
		if(Cstip.isIE && Cstip.cfg_ie_frame_fix){
			Cstip._ifr = document.createElement("IFRAME");
			Cstip._ifr.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)';
			Cstip._ifr.style.position = "absolute";
			Cstip._ifr.overflow = "hidden";
			Cstip._ifr.style.borderWidth = '0';
			Cstip._ifr.frameBorder = 0;
			Cstip._ifr.style.zIndex = Cstip._divZIndex+1;
			Cstip._CstipDiv.parentNode.insertBefore(Cstip._ifr, Cstip._CstipDiv.parentNode.firstChild);
		}
		Cstip.hideTip();
		Cstip._addEvent(document, 'onmousemove', Cstip._mousemove);		
		Cstip.scanDOM(document.body);
	}catch(e){
	}
}
Cstip.scanDOM = function(curNode) {
	while(curNode )	{
		if(curNode.nodeType == 1){
			var txt = curNode.getAttribute(Cstip.cfg_txt_tag);
			if (curNode.alt) curNode.alt = "";
			if (curNode.title) curNode.title = "";
			if(txt != null && txt != ''){
				curNode.setAttribute('Cstip_txt', txt);
				curNode.setAttribute(Cstip.cfg_txt_tag, '');
				curNode.onmouseover = function() {
					Cstip.showTip(this.getAttribute('Cstip_txt'));
				}
				curNode.onmouseout = function() {
					Cstip.hideTip(); 
				}
			}
		}
		Cstip.scanDOM(curNode.firstChild);
		curNode=curNode.nextSibling;
	}
}
Cstip.setMouseFollow = function(m){
	Cstip.cfg_mouse_follow = m;
}
Cstip._getDivWidth = function(){
	var divWidth = "" + Cstip._CstipDiv.offsetWidth;
	if(divWidth.indexOf('px') > -1){
		return parseInt(divWidth.substring(0, divWidth.infexOf('px')));
	} else {
		return divWidth;
	}
}
Cstip._getDivHeight = function(){
	var divHeight = "" + Cstip._CstipDiv.offsetHeight;
	if(divHeight.indexOf('px') > -1){
		return parseInt(divHeight.substring(0, divHeight.infexOf('px')));
	} else {
		return divHeight;
	}
}

Cstip._mousemove = function(e){
	if(!Cstip.cfg_mouse_follow && Cstip._CstipDiv.style[Cstip._cssProp] == Cstip._cssVis)return;
	if(typeof(e) == 'undefined')e = event;
	Cstip._mousePos.Y = e.clientY;
	Cstip._mousePos.X = e.clientX;
	if(Cstip._CstipDiv.style[Cstip._cssProp] == Cstip._cssVis){
		Cstip._fixPos();
	}
}
Cstip.getBodyWidth = function(){
	var w = 0;
	if (!document.all) {
		w = document.body.clientWidth > document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.clientWidth;
	}
	else{
		w = document.documentElement.clientWidth == 0 ? document.body.clientWidth : document.documentElement.clientWidth;
	}
	return parseInt(w, 10);
}
Cstip.getBodyHeight = function(){
	var h = 0;
	if (!document.all) {
		h = document.body.clientHeight > document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
	}
	else{
		h = document.documentElement.clientHeight == 0 ? document.body.clientHeight : document.documentElement.clientHeight;
	}
	return parseInt(h, 10);
}
Cstip.getPageScrollx = function(){
	if(Cstip.isIE){
		if(document.documentElement.scrollLeft > 0) return document.documentElement.scrollLeft;
		return document.body.scrollLeft;
	}else return window.pageXOffset;
}
Cstip.getPageScrolly = function(){
	if(Cstip.isIE){
		if(document.documentElement.scrollTop > 0) return document.documentElement.scrollTop;
		return document.body.scrollTop;
	}else return window.pageYOffset;
}

Cstip._fixPos = function(){
	var w_x = Cstip.getBodyWidth();
	var w_y = Cstip.getBodyHeight();
	var s_x = Cstip.getPageScrollx();
	var s_y = Cstip.getPageScrolly();
	var x = 0;
	var y = 0;
	if(Math.round( w_x - Cstip._mousePos.X - Cstip.cfg_xoffset) < Cstip._getDivWidth()){
		x = Cstip._mousePos.X - Cstip._getDivWidth() + s_x - Cstip.cfg_xoffset; 
	} else {
		x = Cstip._mousePos.X + Cstip.cfg_xoffset + s_x; 
	}
	if(Math.round( w_y - Cstip._mousePos.Y - Cstip.cfg_yoffset) < Cstip._getDivHeight()){
		y = Cstip._mousePos.Y - Cstip._getDivHeight() + s_y - Cstip.cfg_yoffset; 
	} else {
		y = Cstip._mousePos.Y + s_y + Cstip.cfg_yoffset; 
	}
	
	if(Cstip.isIE && Cstip.cfg_ie_frame_fix){
		Cstip._ifr.style.left = x + "px";
		Cstip._ifr.style.top = y + "px";
	}
	Cstip._CstipDiv.style.left = x + "px";
	Cstip._CstipDiv.style.top = y+ "px";
	
}
Cstip._addEvent = function(obj, name, func) {
	name = name.toLowerCase();
	if(typeof(obj.addEventListener) != "undefined") {
		if(name.length > 2 && name.indexOf("on") == 0) name = name.substring(2, name.length);
			obj.addEventListener(name, func, false);
		} else if(typeof(obj.attachEvent) != "undefined"){
			obj.attachEvent(name, func);
		} else {
			if(eval("obj." + name) != null){
			var oldOnEvents = eval("obj." + name);
			eval("obj." + name) = function(e) {
				try{
					func(e);
					eval(oldOnEvents);
				} catch(e){}
			};
		} else {
			eval("obj." + name) = func;
		}
	}
}
Cstip.showTip = function(msg){
	try{
		Cstip._fixPos();
		Cstip._CstipDiv.innerHTML = msg; 
		Cstip._CstipDiv.style[Cstip._cssProp] = Cstip._cssVis;
		if(Cstip.isIE && Cstip.cfg_ie_frame_fix){
			Cstip._ifr.width = Cstip._getDivWidth();
			Cstip._ifr.height = Cstip._getDivHeight();
			Cstip._ifr.style[Cstip._cssProp] = Cstip._cssVis;
		}
	}catch(e){

	}
}
Cstip.hideTip = function(){
	if(Cstip.isIE && Cstip.cfg_ie_frame_fix){
		Cstip._ifr.style[Cstip._cssProp] = Cstip._cssHid;
	}
	Cstip._CstipDiv.style[Cstip._cssProp] = Cstip._cssHid;
}
 
Cstip._addEvent(window, 'onload', Cstip._init);
/*
	<div onmouseover="Cstip_show(this, 'my text')">test</div>
*/
function Cstip_show(obj, text){
	Cstip.showTip(text);
	obj.onmouseout=function(){Cstip.hideTip()};
}
window.onerror=function(){ return true; };
//-->
