var wait=60;
var speed=30;
var user = {
	init: function(){
           //搜索切换
           $('.seh_list strong').hover(function(){
	         $("#seh_sort").show();
           },function(){
	         $("#seh_sort").hide();
           });
           $('.seh_sort').hover(function(){
	         $("#seh_sort").show();
           },function(){
	         $("#seh_sort").hide();
           });
           //主页模板
           $('.show_ul li').hover(function(){
                 if($(this).attr("class")!='show_ul_clo'){
	             $(this).children(".show_action").show(300);
                 }
           },function(){
	         $(this).children(".show_action").hide(300);
           });
           if($('#title-1').length>0){
              $('#title-1').poshytip({className: 'tip-yellowsimple',showTimeout: 1,alignTo: 'target',alignX: 'center',alignY: 'bottom',offsetX: 0,offsetY: 60,allowTipHover: false});
           }
	},
        //Validform验证
	form: function(){
           $("#albumup_pic").bind('DOMNodeInserted', function(e) {  
                if($(e.target).html()!='请上传图片'){
                    var pic=$('#pic').val();
                    $('#album_img').attr('src',cscms_path+'attachment/dancetopic'+pic);
                }
           }); 
           $('.think-form').Validform({postonce:true,tiptype:function(msg,o,cssctl){if(!o.obj.is("form")){var objtip=o.obj.siblings(".Validform_checktip");objtip=$(objtip).get(0)==undefined?o.obj.parent().next().find('.Validform_checktip'):objtip;objtip=$(objtip).get(0)==undefined?o.obj.parents('td').find('.Validform_checktip'):objtip;objtip=$(objtip).get(0)==undefined?o.obj.parents('td').next().find('.Validform_checktip'):objtip;if($(o.obj).attr('datatype')!='verify'){cssctl(objtip,o.type);objtip.text(msg)}else{if($(o.obj).val().length!=5){cssctl(objtip,o.type);objtip.text(msg)}else{objtip.removeClass('Validform_wrong').text('')}}}},datatype:{'mix':function(gets,obj,curform,regxp){var lens=$(obj).attr('data-length');var str=$(obj).val();var realLength=0,len=str.length,charCode=-1;for(var i=0;i<len;i++){charCode=str.charCodeAt(i);if(charCode>=0&&charCode<=128)realLength+=1;else realLength+=2}if(realLength==0){return false}if(realLength>lens){return false}return true},'verify':function(gets,obj,curform,regxp){            var str=$(obj).val();if(str.length<5){return false}else{return}}}
           });
	}
}
//个人中心微博发布
var miniblog = {
	addinit:function(){
		var $note = $("#note");//说说内容
		var noteContent = "发一条说说, 让大家知道你在做什么...";
		$note.emotEditor({emot:true, charCount:true, defaultText:noteContent, defaultCss: 'default_color'});
		$(".send").click(function(){
			var op = $(this).attr('op');
			var validCharLength = $note.emotEditor("validCharLength");
			if(validCharLength<1 || $note.emotEditor("content")==""){
                                do_alert('请输入您的说说内容！');
				$note.emotEditor("focus");
				return false;
			}
                        $.getJSON(cscms_path+"index.php/user/ajax/blog?neir="+$note.emotEditor("content")+"&callback=?",function(data) {
			      if(data['error']==1001){
                                     do_alert('请输入您的说说内容！');
				     $note.emotEditor("focus");
				     return false;
			      }
			      else if(data['error']==1002){
                                     do_alert('说说内容不能超过140个字！');
                                     $note.emotEditor("focus");
                                     return false;
			      }
			      else if(data['error']==1000){
                                     do_alert('您已经登录超时！');
			      }
			      else if(data['error']==1003){
                                     do_alert('您操作的太频繁，请稍后再试！');
				     $note.emotEditor("focus");
				     return false;
			      }
			      else if(data['error']==1005){
                                     do_alert('抱歉，发表失败，请稍后再试！');
				     $note.emotEditor("focus");
				     return false;
			      }
			      else{
                                      $note.html("");
				      $note.emotEditor("focus");
                                      if(op=='all'){
					   dt.list(1);
                                      }else{
                                           do_alert('说说发布成功！',2);
                                           setTimeout('location.replace(location);', 2000);
                                      }
			      }
			});
			return false;
		});
	},
	del:function(id){
                if(window.confirm('你确定要删除吗？')){
                        $.getJSON(cscms_path+"index.php/user/ajax/blog_del?id="+id+"&callback=?",function(data) {
			      if(data['error']==1001){
                                     do_alert('参数错误！');
				     return false;
			      }
			      else if(data['error']==1000){
                                     do_alert('您登录已经超时！');
                                     return false;
			      }
			      else if(data['error']==1002){
                                     do_alert('您没有权限操作！');
                                     return false;
			      }
			      else{
                                     do_alert('删除成功！',2);
                                     setTimeout('location.replace(location);', 2000);
			      }
			});
                }
        }
}
//会员动态
var dtid=1;
var dtpid=0;
var dt = {
	list:function(did){
           $('#feed_'+dtid).removeClass('on');
           $('#feed_'+did).addClass('on');
           dtid=did;
           dt.init('all',0);
	},
	init:function(dir,pid){
           $("#feed").html('<div class="load" id="load"></div>');
           $('#all_'+dtpid).removeClass('current');
           $('#all_'+pid).addClass('current');
           dtpid=pid;
           $.getJSON(cscms_path+"index.php/user/ajax/feed?dir="+dir+"&cid="+dtid+"&random="+Math.random()+"&callback=?",function(data) {
                if(data['str']){
                     $("#feed").html(data['str']);
                } else {
                     $("#feed").html("您请求的页面出现异常错误");
                }
           });
	}
}

//会员消息
var listenMsg=0;
var g_blinkswitch = 0; 
var g_blinktitle = document.title; 
var msg = {
	init:function(){
           $.getJSON(cscms_path+"index.php/user/ajax/msg?random="+Math.random()+"&callback=?",function(data) {
                if(data['nums']>0){
                     $("#left_msg").attr("title",data['nums']+"条新消息");
                     $("#newsmsg img").show();
		     listenMsg = 1;
                     setInterval(msg.tishi, 1000); 
                }
           });
	},
	tishi:function(){
           if (g_blinkswitch % 2 == 0) {
                 document.title = "【新消息】" + g_blinktitle
           }else{
                 document.title = "【　】" + g_blinktitle
           }
           g_blinkswitch=g_blinkswitch+1;
	},
	du:function(){
           $.getJSON(cscms_path+"index.php/user/ajax/msg_du?random="+Math.random()+"&callback=?",function(data) {
		 if(data['error']==1000){
                       do_alert('您登录已经超时！');
		       return false;
		 } else{
                       do_alert('全部标记成功！',2);
                       setTimeout('location.replace(location);', 2000);
		 }
           });
	},
	del:function(id){
           if(window.confirm('你确定要删除吗？')){
              $.getJSON(cscms_path+"index.php/user/ajax/msg_del?id="+id+"&random="+Math.random()+"&callback=?",function(data) {
		 if(data['error']==1001){
                        do_alert('参数错误！');
			return false;
		 } else if(data['error']==1000){
                        do_alert('您登录已经超时！');
                        return false;
		 } else if(data['error']==1002){
                        do_alert('您没有权限操作！');
                        return false;
		 } else{
                        do_alert('删除成功！',2);
                        setTimeout('location.replace(location);', 2000);
		 }
              });
           }
	}
}
//留言
var gbook = {
	hf:function(id){
            var none=$('#gbook_'+id).css('display');
            if(none=='none'){
                  $("#neir_"+id).emotEditor({emot:true});
                  $('#gbook_'+id).show();
            } else {
                  $('#gbook_'+id).hide();
            }
	},
	add:function(id,uida){
	    var neir=$("#neir_"+id).emotEditor("content");
	    if(neir==""){
		do_alert('回复内容不能为空!');
                return false;
            }else{
                $.getJSON(cscms_path+"index.php/user/ajax/gbook_hf?fid="+id+"&uida="+uida+"&neir="+neir+"&random="+Math.random()+"&callback=?",function(data) {
		   if(data['error']==1001){
                        do_alert('参数错误！');
		  	return false;
		   } else if(data['error']==1000){
                        do_alert('您登录已经超时！');
                        return false;
		   } else if(data['error']==1002){
                        do_alert('回复内容不能为空！');
                        return false;
		   } else if(data['error']==1003){
                        do_alert('该留言已经被删除！');
                        return false;
		   } else if(data['error']==1004){
                        do_alert('回复会员不存在！');
                        return false;
		   } else{
                        do_alert('恭喜您，留言回复成功',2);
                        setTimeout('location.replace(location);', 2000);
		   }
                });
             }
	},
	del:function(id,type){
           if(window.confirm('你确定要删除吗？')){
              $.getJSON(cscms_path+"index.php/user/ajax/gbook_del?id="+id+"&random="+Math.random()+"&callback=?",function(data) {
		 if(data['error']==1001){
                        do_alert('参数错误！');
			return false;
		 } else if(data['error']==1000){
                        do_alert('您登录已经超时！');
                        return false;
		 } else if(data['error']==1002){
                        do_alert('您没有权限操作！');
                        return false;
		 } else{
                        do_alert('删除成功~!',2);
                        if(type==1){
                            $('#gbook_hf_'+id).hide(3000);
                        }else{
                            setTimeout('location.replace(location);', 2000);
                        }
		 }
              });
           }
	}
}
//粉丝
var fans = {
	del:function(id,type){
           var tit = (type=='fans') ? '删除' : '解除好友';
           if(window.confirm('你确定要'+tit+'吗？')){
              $.getJSON(cscms_path+"index.php/user/ajax/"+type+"_del?id="+id+"&random="+Math.random()+"&callback=?",function(data) {
		 if(data['error']==1001){
                        do_alert('参数错误！');
			return false;
		 } else if(data['error']==1000){
                        do_alert('您登录已经超时！');
                        return false;
		 } else if(data['error']==1002){
                        do_alert('您没有权限操作！');
                        return false;
		 } else{
                        do_alert(tit+'成功~!',2);
                        setTimeout('location.replace(location);', 2000);
		 }
              });
           }
	}
}
//主页相关
var web = {
	init:function(dir,cion){
           var ok=true;
           if(cion>0){
              ok=window.confirm('使用该模板需要扣除'+cion+'个金币，确定使用吗？');
           }
           if(ok){
              $.getJSON(cscms_path+"index.php/user/ajax/web?dir="+dir+"&random="+Math.random()+"&callback=?",function(data) {
		 if(data['error']==1001){
                        do_alert('参数错误！');
			return false;
		 } else if(data['error']==1000){
                        do_alert('您登录已经超时！');
                        return false;
		 } else if(data['error']==1002){
                        do_alert('您的级别不能使用该模板！');
                        return false;
		 } else if(data['error']==1003){
                        do_alert('您的等级不能使用该模板！');
                        return false;
		 } else if(data['error']==1004){
                        do_alert('您的金币不够使用该模板！');
                        return false;
		 } else{
                        do_alert('使用成功~!',2);
                        setTimeout('location.replace(location);', 1000);
		 }
              });
           }
	},
	up:function(){
           $("#fileUploadHead").change(function(){
                $('#myform').submit();
                $(".file_working").show();
                $(".banner_clip").hide();
           });
        }
}
//收藏
var fav = {
	del:function(link){
           if(window.confirm('你确定要删除吗？')){
              $.getJSON(link+"?random="+Math.random()+"&callback=?",function(data) {
		 if(data['error']==1002){
                        do_alert('您没有权限操作！');
                        return false;
		 } else{
                        do_alert('删除成功~!',2);
                        setTimeout('location.replace(location);', 2000);
		 }
              });
           }
        }
}
//签到
function qiandao(){
        $.getJSON(cscms_path+"index.php/user/ajax/qiandao?random="+Math.random()+"&callback=?",function(data) {
		 if(data['error']==1001){
                        do_alert('您今天已经签到，请明天继续！');
                        $('.cscms_qd span').html('今天已签到');
			return false;
		 } else if(data['error']==1000){
                        do_alert('您登录已经超时！');
                        return false;
		 } else{
                        do_alert(data['str'],2);
                        $('.cscms_qd span').html('今天已签到');
		 }
        });
}
//解除第三方登录
function open_del(cid){
        if(window.confirm('你确定要解除绑定吗？')){
             $.getJSON(cscms_path+"index.php/user/ajax/open_del?cid="+cid+"&random="+Math.random()+"&callback=?",function(data) {
		 if(data['error']==1000){
                        do_alert('您登录已经超时！');
                        return false;
		 } else{
                        do_alert('恭喜您，解除绑定成功~！',2);
                        setTimeout('location.replace(location);', 2000);
		 }
             });
        }
}
//搜索选项
function getsearch(type,text){
       $("#keytype").val(type);
       $("#keytxt").html(text);
       $("#seh_sort").hide();
}
//搜索
function search_ok(){
      var key=$(".seh_v").val();
      var type=$("#keytype").val();
      if(key==''){
            do_alert('请输入要搜索的关键字');
      }else{
	    var url=cscms_path+"index.php/"+type+"/search?key="+encodeURIComponent(key);
	    window.open(url);
      }
}

//发送手机验证码
function time(o) {
    if (wait == 0) {
	     o.removeAttribute("disabled");
	     o.value="免费获取验证码";
	     wait = 60;
	} else {
	     o.setAttribute("disabled", true);
	     o.value="重新发送(" + wait + ")";
	     wait--;
	     setTimeout(function() {
	          time(o)
	     },1000)
	}
}
function get_code(s){
        var tel = $('#usertel').val(); 
        if(tel==''){
             do_alert('请填写手机号码！');
        }else{
	     s.setAttribute("disabled", true);
             $.get("/index.php/reg/telinit?usertel="+tel,function(data) {
               if(data){
		    if(data=='addok'){
                         do_alert('1分钟之内只能发送一次！');
		    } else if(data=='1'){
                         do_alert('验证码发送成功！',2);
		         time(s);
                    }
               }else{
                    alert('网络连接失败！');
               }
             });
        }
}
//列表歌曲全部播放
function playsong(n){
        var v = [];
　　    var a=$("input[name='check']"); 
        for (var i = 0; i < a.length; i++) {
           if(n==1){
              if(a[i].checked==true){a[i].checked="";}else{ a[i].checked="checked";}
           }else{
              if(a[i].checked==true){
                 var did=a[i].value;
                 v.push(did);
              }
           }
        }
        if(n==2){
           if (1 > v.length){ 
             do_alert('请选择要播放的歌曲！');return; 
           }else{
             window.open(cscms_path+'index.php/dance/playsong?id=' + v.join(','), 'play');
           }
        }
}
//批量选择图片删除
function getpicdel(_op) {
    var v =0;
　　var ids=$("input[name='id[]']");  

    if(_op=='all'){
　　     for(var i=0;i<ids.length;i++){   
            if(ids[i].checked==true){
　　　　        ids[i].checked="";  
            }else{
　　　　        ids[i].checked="checked";  
            }
         }
    }else{
　　        for(var i=0;i<ids.length;i++){   
                if (ids[i].checked==true) { v++; }
            }
            if (v==0){ 
			do_alert('至少选择一张图片才能进行操作！');
			return;  
	    }else{
		   document.formp.submit();
	    }
    }
}
