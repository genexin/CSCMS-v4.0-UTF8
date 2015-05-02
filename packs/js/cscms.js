/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2015-01-25
 */
(function(a){typeof a.CMP=="undefined"&&(a.CMP=function(){var b=/msie/.test(navigator.userAgent.toLowerCase()),c=function(a,b){if(b&&typeof b=="object")for(var c in b)a[c]=b[c];return a},d=function(a,d,e,f,g,h,i){i=c({width:d,height:e,id:a},i),h=c({allowfullscreen:"true",allowscriptaccess:"always"},h);var j,k,l=[];if(g){j=g;if(typeof g=="object"){for(var m in g)l.push(m+"="+encodeURIComponent(g[m]));j=l.join("&")}h.flashvars=j}k="<object ",k+=b?'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ':'type="application/x-shockwave-flash" data="'+f+'" ';for(var m in i)k+=m+'="'+i[m]+'" ';k+=b?'><param name="movie" value="'+f+'" />':">";for(m in h)k+='<param name="'+m+'" value="'+h[m]+'" />';return k+="</object>",k},e=function(c){var d=document.getElementById(c);if(!d||d.nodeName.toLowerCase()!="object")d=b?a[c]:document[c];return d},f=function(a){if(a){for(var b in a)typeof a[b]=="function"&&(a[b]=null);a.parentNode.removeChild(a)}},g=function(a){if(a){var c=typeof a=="string"?e(a):a;if(c&&c.nodeName.toLowerCase()=="object")return b?(c.style.display="none",function(){c.readyState==4?f(c):setTimeout(arguments.callee,15)}()):c.parentNode.removeChild(c),!0}return!1};return{create:function(){return d.apply(this,arguments)},write:function(){var a=d.apply(this,arguments);return document.write(a),a},get:function(a){return e(a)},remove:function(a){return g(a)}}}());var b=function(b){b=b||a.event;var c=b.target||b.srcElement;if(c&&typeof c.cmp_version=="function"){var d=c.skin("list.tree","maxVerticalScrollPosition");if(d>0)return c.focus(),b.preventDefault&&b.preventDefault(),!1}};a.addEventListener&&a.addEventListener("DOMMouseScroll",b,!1),a.onmousewheel=document.onmousewheel=b})(window);

var DomainUrl = top.location.hostname;
var cscms_zd = 0;

//浏览器版本
var browser = {};
var ua = navigator.userAgent.toLowerCase();
var browserStr;
(browserStr = ua.match(/msie ([\d]+)/)) ? browser.ie = browserStr[1] :
(browserStr = ua.match(/firefox\/([\d]+)/)) ? browser.firefox = browserStr[1] :
(browserStr = ua.match(/chrome\/([\d]+)/)) ? browser.chrome = browserStr[1] :
(browserStr = ua.match(/opera.([\d]+)/)) ? browser.opera = browserStr[1] :
(browserStr = ua.match(/version\/([\d]+).*safari/)) ? browser.safari = browserStr[1] : 0;
if (browser.ie==6) {
	window.attachEvent("onunload", function() {
		for ( var id in jQuery.cache ) {
			if ( jQuery.cache[ id ].handle ) {
				try {
					jQuery.event.remove( jQuery.cache[ id ].handle.elem );
				} catch(e) {}
			}
		}
	});
}

//登录状态
function cscms_login(){
     $.getJSON(cscms_loginlink+"?random="+Math.random()+"&callback=?",function(data) {
           if(data['str']){
                $("#cscms_login").html(data['str']);
           } else {
                $("#cscms_login").html("您请求的页面出现异常错误");
           }
     });
}
//登录
function cscms_logadd(){
     var name=$("#cscms_name").val();
     var pass=$("#cscms_pass").val();
     if(name=='' || pass==""){
           do_alert('帐号、密码不能为空!');
     }else{
           $.getJSON(cscms_loginaddlink+"?username="+name+"&userpass="+pass+"&random="+Math.random()+"&callback=?",function(data) {
               if(data['error']=='10001'){ //用户名为空
                    do_alert('帐号不能为空!');
               } else if(data['error']=='10002'){ //密码为空
                    do_alert('密码不能为空!');
               } else if(data['error']=='10003'){ //帐号不存在
                    do_alert('您的帐号不存在!');
               } else if(data['error']=='10004'){ //密码错误
                    do_alert('您的密码错误!');
               } else if(data['error']=='10005'){ //帐号被锁定
                    do_alert('您的帐号被锁定!');
               } else if(data['error']=='10006'){ //登入成功
                    cscms_login();
               } else if(data['error']=='10007'){ //邮件未激活
                    do_alert('您的帐号未激活，请去邮箱激活!');
               } else { 
                   do_alert(data['error']);
               }
           });
     }
}
//退出登录
function cscms_logout(){
     $.getJSON(cscms_logoutlink+"?callback=?",function(data) {
           if(data['error']=='10001'){
                cscms_login();
           } else {
                do_alert('网络故障，连接失败!');
           }
     });
}
//评论列表
function cscms_pl(_pages,_id,_fid){
     $.getJSON(cscms_path+"index.php/pl/index/"+dir+"/"+did+"/"+cid+"/"+_pages+"?random="+Math.random()+"&callback=?",function(data) {
           if(data['str']){
                $("#cscms_pl").html(data['str']);
                if(cscms_zd>0){
                   click_scroll('cscms_pl');
                }
                cscms_zd=1;
           } else {
                $("#cscms_pl").html("您请求的页面出现异常错误");
           }
     });
}
//提交评论
function cscms_pladd(){
     var neir=$("#cscms_pl_content").val();
     var token=$("#pl_token").val();
     if(neir==""){
           do_alert('评论内容不能为空!');
     }else{
           $.getJSON(cscms_path+"index.php/pl/add?dir="+dir+"&token="+token+"&did="+did+"&cid="+cid+"&neir="+encodeURIComponent(neir)+"&random="+Math.random()+"&callback=?",function(data) {
                   var msg=data['error'];
	           if(msg == "10000"){
	                     do_alert('站长已经关闭评论！');
	           } else if(msg == "10001"){
	                     do_alert('参数错误！');
	           } else if(msg == "10002"){
	                     do_alert('非法提交数据！');
	           } else if(msg == "10003"){
	                     do_alert('内容不能为空！');
	           } else if(msg == "10004"){
	                     do_alert('您还没有登录，请先登录！');
	           } else if(msg == "10006"){
                             cscms_pl(1,0,0);
	           } else if(msg == "10005"){
	                     do_alert('对不起，评论失败，稍后再试！');
	           } else if(msg == "10007"){
	                     do_alert('休息下，在评论！');
	           } else {
	                     do_alert(msg);
	           }
           });
     }
}
//评论回复
function cscms_plhf(_id,_text){
     var neir=$("#cscms_pl_hf_"+_id).val();
     var token=$("#pl_token").val();
     if(neir=="" || neir==_text){
           do_alert('评论回复内容不能为空!');
     }else{
           $.getJSON(cscms_path+"index.php/pl/add?dir="+dir+"&token="+token+"&fid="+_id+"&did="+did+"&cid="+cid+"&neir="+encodeURIComponent(neir)+"&random="+Math.random()+"&callback=?",function(data) {
                   var msg=data['error'];
	           if(msg == "10000"){
	                     do_alert('站长已经关闭评论！');
	           } else if(msg == "10001"){
	                     do_alert('参数错误！');
	           } else if(msg == "10002"){
	                     do_alert('非法提交数据！');
                             cscms_pl(1,0,0);
	           } else if(msg == "10003"){
	                     do_alert('内容不能为空！');
	           } else if(msg == "10004"){
	                     do_alert('您还没有登录，请先登录！');
	           } else if(msg == "10006"){
                             cscms_pl(1,0,0);
	           } else if(msg == "10005"){
	                     do_alert('对不起，评论回复失败，稍后再试！');
	           } else if(msg == "10007"){
	                     do_alert('休息下，在评论！');
	           } else {
	                     do_alert(msg);
	           }
           });
     }
}
//删除评论
function cscms_pldel(_id){
     var token=$("#pl_token").val();
     if(confirm("系统提示:您确定要删除吗!")){
           $.getJSON(cscms_path+"index.php/pl/del?id="+_id+"&token="+token+"&callback=?",function(data) {
                   var msg=data['error'];
	           if(msg == "10000"){
	                     do_alert('您登陆已经超时！');
	           } else if(msg == "10001"){
	                     do_alert('参数错误！');
	           } else if(msg == "10002"){
	                     do_alert('非法提交数据！');
                             cscms_pl(1,0,0);
	           } else if(msg == "10003"){
	                     do_alert('您不能删除别人的评论！');
	           } else if(msg == "10004"){
                             cscms_pl(1,0,0);
	           } else {
	                     do_alert(msg);
	           }
           });
     }
}
//留言列表
function cscms_gbook(_pages,_id,_fid){
     $.getJSON(cscms_path+"index.php/gbook/lists/"+_pages+"?random="+Math.random()+"&callback=?",function(data) {
           if(data['str']){
                $("#cscms_gbook").html(data['str']);
                if(cscms_zd>0){
                   click_scroll('cscms_gbook');
                }
                cscms_zd=1;
           } else {
                $("#cscms_gbook").html("您请求的页面出现异常错误");
           }
     });
}
//提交留言
function cscms_gbookadd(){
     var token=$("#gbook_token").val();
     var neir=$("#cscms_gbook_content").val();
     if(neir==""){
           do_alert('内容不能为空!');
     } else {
           $.post(cscms_path+"index.php/gbook/add",{token: token,neir: neir},function(data) {
                   var msg=data['error'];
	           if(msg == "10000"){
	                     do_alert('站长已经关闭留言！');
	           } else if(msg == "10001"){
	                     do_alert('非法提交数据！');
                             cscms_gbook(1,0,0);
	           } else if(msg == "10002"){
	                     do_alert('内容不能为空！');
	           } else if(msg == "10004"){
                             cscms_gbook(1,0,0);
	           } else if(msg == "10003"){
	                     do_alert('对不起，留言失败，稍后再试！');
	           } else {
	                     do_alert(msg);
	           }
           },"json");
     }
}
//会员主页留言列表
function cscms_home_gbook(_pages){
     $.getJSON(cscms_path+"index.php/home/gbook/ajax/"+uid+"/"+_pages+"?random="+Math.random()+"&callback=?",function(data) {
           if(data['str']){
                $("#cscms_gbook").html(data['str']);
                if(cscms_zd>0){
                   click_scroll('cscms_gbook');
                }
                cscms_zd=1;
           } else {
                $("#cscms_gbook").html("您请求的页面出现异常错误");
           }
     });
}
//提交会员主页留言
function cscms_home_gbookadd(){
     var neir=$("#cscms_gbook_content").val();
     var token=$("#gbook_token").val();
     if(neir==""){
           do_alert('留言内容不能为空!');
     }else{
           $.getJSON(cscms_path+"index.php/home/gbook/add?token="+token+"&uid="+uid+"&neir="+encodeURIComponent(neir)+"&random="+Math.random()+"&callback=?",function(data) {
                   var msg=data['error'];
	           if(msg == "10000"){
	                     do_alert('参数错误！');
	           } else if(msg == "10001"){
	                     do_alert('非法提交数据！');
                             cscms_home_gbook(1);
	           } else if(msg == "10002"){
	                     do_alert('内容不能为空！');
	           } else if(msg == "10003"){
	                     do_alert('您还没有登录，请先登录！');
	           } else if(msg == "10004"){
	                     do_alert('对不起，留言失败，稍后再试！');
	           } else if(msg == "10005"){
                             cscms_home_gbook(1);
	           } else if(msg == "10006"){
	                     do_alert('休息下，在留言！');
	           } else {
	                     do_alert(msg);
	           }
           });
     }
}
//会员主页留言回复
function cscms_home_gbookhf(_id,_text){
     var neir=$("#cscms_gbook_hf_"+_id).val();
     var token=$("#gbook_token").val();
     if(neir=="" || neir==_text){
           do_alert('回复内容不能为空!');
     }else{
           $.getJSON(cscms_path+"index.php/home/gbook/add?token="+token+"&fid="+_id+"&uid="+uid+"&neir="+encodeURIComponent(neir)+"&random="+Math.random()+"&callback=?",function(data) {
                   var msg=data['error'];
	           if(msg == "10000"){
	                     do_alert('参数错误！');
	           } else if(msg == "10001"){
	                     do_alert('非法提交数据！');
                             cscms_home_gbook(1);
	           } else if(msg == "10002"){
	                     do_alert('内容不能为空！');
	           } else if(msg == "10003"){
	                     do_alert('您还没有登录，请先登录！');
	           } else if(msg == "10004"){
	                     do_alert('对不起，留言失败，稍后再试！');
	           } else if(msg == "10005"){
                             cscms_home_gbook(1);
	           } else if(msg == "10006"){
	                     do_alert('休息下，在留言！');
	           } else {
	                     do_alert(msg);
	           }
           });
     }
}
//删除会员主页留言
function cscms_home_gbookdel(_id){
     var token=$("#gbook_token").val();
     if(confirm("系统提示:您确定要删除吗!")){
           $.getJSON(cscms_path+"index.php/home/gbook/del?id="+_id+"&token="+token+"&callback=?",function(data) {
                   var msg=data['error'];
	           if(msg == "10000"){
	                     do_alert('您登陆已经超时！');
	           } else if(msg == "10001"){
	                     do_alert('参数错误！');
	           } else if(msg == "10002"){
	                     do_alert('非法提交数据！');
                             cscms_home_gbook(1);
	           } else if(msg == "10003"){
	                     do_alert('您不能删除别人的留言！');
	           } else if(msg == "10004"){
                             cscms_home_gbook(1);
	           } else {
	                     do_alert(msg);
	           }
           });
     }
}
//滚动至指定位置
function click_scroll(_id) {
     var scroll_offset = $("#"+_id+"").offset();  //得到pos这个div层的offset，包含两个值，top和left  
     $("body,html").animate({
           scrollTop:scroll_offset.top  //让body的scrollTop等于pos的top，就实现了滚动   
     },0);
}
//复制
var cscms_share_url,cscms_share_id,cscms_share_title;
function cscms_copy(url,id,title){
     cscms_share_url=url;
     cscms_share_id=id;
     cscms_share_title=title;
     cscms_inc_js(cscms_path+'packs/js/jquery.zclip.min.js');
     setTimeout("copy_cscms();",500);
}
function copy_cscms() {
     var clip = new ZeroClipboard.Client(); // 新建一个对象
     clip.setHandCursor( true );
     clip.setText(cscms_share_url); // 设置要复制的文本。
     clip.addEventListener( "mouseUp", function(client) {
	 do_alert(cscms_share_title,2);
     });
     clip.glue(cscms_share_id); // 和上一句位置不可调换
     return true;
}
//cmp音频播放器
function mp3_play() {
     var flashvars = { 
		api : "cmp_loaded",
		skins : mp3_t+"packs/vod_player/cmp/mp3.swf",
		auto_play : "1",
                play_mode : "1",
                play_id   : "1",
		lists     : mp3_p+'/url/cmp/'+mp3_i+'.mp3'
     };
     var html = CMP.create("cmp", mp3_w+"px", mp3_h+"px", mp3_t+"packs/vod_player/cmp/cmp.swf", flashvars, {wmode:"transparent"});
     document.writeln(html);
}
//jp音频播放器带LRC
function mp3_jplayer() {
     cscms_inc_js(mp3_p+'/url/jp/'+mp3_i);
     document.write('<link href="'+cscms_path+'packs/vod_player/jplayer/skin/lrc/css.css" rel="stylesheet" type="text/css" />');
     document.write('<script type="text/javascript" src="'+cscms_path+'packs/vod_player/jplayer/js/lrc.js"></script>');
     document.write('<script type="text/javascript" src="'+cscms_path+'packs/vod_player/jplayer/js/jquery.jplayer.min.js"></script>');
     document.write('<div id="cscms_lyric" onmouseover="$(\'.seegc\').show();" onmouseout="$(\'.seegc\').hide();"><div class="seegc" style="display: none;"><a href="'+mp3_l+'" target="_blank">下<br>载<br>歌<br>词</a></div><p id="LR1"></p><p id="LR2"></p><p id="LR3"></p><p id="LR4"></p><p id="LR5"></p><p id="LR6"></p><p id="LR7"></p></div><div class="cscms_jplayer"><div id="radioPlayer"class="jp-jplayer"></div><div id="jp_container_1"class="jp-audio"><div class="jp-type-single"><div class="jp-interface clearfix"><div class="playerMain-01"><p><span id="PlayStateTxt">正在播放:</span><span id="play_musicname">'+mp3_n+'</span></p><div class="jp-time-holder"><div class="jp-current-time">00:00</div>/<div class="jp-duration">00:00</div></div></div><div class="playerMain-02"><div class="jp-progress"><div class="jp-seek-bar"><div class="jp-play-bar"></div></div></div></div>');
     document.write('<div class="playerMain-03"><div class="fl"><ul class="jp-controls"><li><a href="javascript:;"class="jp-play"tabindex="1">播放</a></li><li><a style="display:none;"href="javascript:;"class="jp-pause"tabindex="1">暂停</a></li></ul></div><div class="fr"><ul class="ku-volume"><li><a href="javascript:{};"class="jp-mute"tabindex="1"title="静音">静音</a></li><li><a href="javascript:{};"class="jp-unmute"style="display:none;"tabindex="1"title="取消静音">取消静音</a></li><li class="volume-bar-wrap"><div class="jp-volume-bar"><div class="jp-volume-bar-value"></div></div></li><li><a href="javascript:;"class="jp-volume-max"tabindex="1"title="最大音量">最大音量</a></li></ul></div><p class="ringDown"><a href="'+mp3_x+'"target="_blank">歌曲下载</a></p></div></div>');
     document.write('<div class="jp-no-solution"><span>播放出现故障,您需要更新！</span>对不起，您需要更新您的浏览器到最新版本或更新您的flash播放器版本！<br/><a href="http://get.adobe.com/flashplayer/"target="_blank">点击下载Flash plugin>></a></div></div></div></div>');
     setTimeout("get_jpplay();",1000);
}
function get_jpplay() {
     if($("#radioPlayer").length==0){
          setTimeout("get_jpplay();",1000);
     }else{
          $("#radioPlayer").jPlayer({
             supplied: "mp3,m4a",
             swfPath: cscms_path+"packs/vod_player/jplayer/js",
             wmode: "window",
             ready:function (event){ 
                  if(mp3_u.indexOf(".m4a")>0){
	              $("#radioPlayer").jPlayer("setMedia", {m4a:mp3_u}).jPlayer("play");
                  }else{
	              $("#radioPlayer").jPlayer("setMedia", {mp3:mp3_u}).jPlayer("play");
                  }
                  pu.downloadlrc(0);
                  pu.PlayLrc(0);
             },
             ended: function () {
                  if(mp3_u.indexOf(".m4a")>0){
	              $("#radioPlayer").jPlayer("setMedia", {m4a:mp3_u}).jPlayer("play");
                  }else{
	              $("#radioPlayer").jPlayer("setMedia", {mp3:mp3_u}).jPlayer("play");
                  }
                  pu.downloadlrc(0);
                  pu.PlayLrc(0);
             }
          });
     }
}
//异步加载JS
function cscms_inc_js(path) { 
      var sobj = document.createElement('script'); 
      sobj.type = "text/javascript"; 
      sobj.src = path; 
      var headobj = document.getElementsByTagName('head')[0]; 
      headobj.appendChild(sobj); 
}
//会员上传头像成功返回
function UploadPicSucceed(data) {
    do_alert('恭喜您，保存头像成功！',2);
    setTimeout('location.replace(location);', 2000);
}
//会员上传附件
var layerid=0;
var cscms_tsid=5;
var layersrc,layert,layerw,layerh,cscms_msg;
function cscms_up(dir,fid,type,tsid){
    if(layerid==0){
        cscms_inc_js(cscms_path+'packs/layer/layer.min.js');
        layerid++;
    }
    layerw='50%'
    layerh="50%";
    layert='上传附件';
    layersrc=cscms_path+'index.php/upload?dir='+dir+'&fid='+fid+'&type='+type+'&tsid='+tsid;
    setTimeout("up_cscms();",100);
}
//TAGS标签
function cscms_tags(fid){
    if(layerid==0){
        cscms_inc_js(cscms_path+'packs/layer/layer.min.js');
        layerid++;
    }
    layerw='50%'
    layerh="70%";
    layert='TAGS标签选择';
    layersrc=cscms_path+'index.php/tags?fid='+fid;
    setTimeout("up_cscms();",100);
}
//其他自定义IF层
function cscms_if(title,link,w,h){
    if(layerid==0){
        cscms_inc_js(cscms_path+'packs/layer/layer.min.js');
        layerid++;
    }
    layersrc=link;
    layerw=w;
    layerh=h;
    layert=title;
    setTimeout("up_cscms();",100);
}
function up_cscms(){
    $.layer({
        type: 2,
        title: [
            layert, 
            'background:#2B2E37; height:40px; color:#fff; border:none;'
        ], 
        border:[0],
        area: [layerw, layerh],
        iframe: {src: layersrc}
    })
}
//富文本编辑框
function cscms_editor(ids,sid){
    var editor;
    if(sid==2){
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="'+ids+'"]', {
		      allowFileManager : true,
		      afterBlur: function(){this.sync();}
		});
	});
    }else{
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="'+ids+'"]', {
			resizeType : 1,
			allowPreviewEmoticons : false,
			allowImageUpload : false,
			items : [
				'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
				'insertunorderedlist', '|', 'emoticons', 'image', 'link'],
			afterBlur: function(){this.sync();}
		});
	});
    }
}
//窗口提示
function do_alert( msg ,tid){
    if(layerid==0){
        cscms_inc_js(cscms_path+'packs/layer/layer.min.js');
    }
    cscms_msg='<font color=#111>'+msg+'</font>'; //提示语
    if(tid==undefined || tid==1){
        cscms_tsid=8; //错误提示图
    } else {
        cscms_tsid=1; //成功提示图
    }
    if(layerid==0){
        setTimeout("cscms_alert();",500);
    }else{
        cscms_alert();
    }
}
function cscms_alert(){
    if (typeof(layer) != "undefined") { 
       layerid++;
       layer.msg(cscms_msg,2,cscms_tsid);
    }
}
