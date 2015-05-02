var Music=MusicList=Musicfavor=Musicrand=Musiccai=[];
var bid=did=0;
var type=0;
var sxid=1;
var pu = new Playergeci();
var navigatorName = "Microsoft Internet Explorer"; 
var isIE = false; 
var isIE6 = false; 
if( navigator.appName == navigatorName ){   
    isIE = true;   
}
//IE7以下不支持JP播放器
if(isIE){
    var b_version=navigator.appVersion 
    var version=b_version.split(";"); 
    var trim_Version=version[1].replace(/[ ]/g,"");  
    if(trim_Version=="MSIE6.0" || trim_Version=="MSIE7.0") {
         isIE6 = true;   
    } else if(trim_Version=="MSIE5.0") {
         $('.frame-inner').show();
         $('.mbox-panel').hide();
         $('#feedback').hide();
    }
}

if(isIE6){

     $(document).ready(function(){
           var e = document.getElementById('mediaPlayerObj');
	   $('.vol-slider-handle').attr('vol', 70);
    	   $('.vol-slider-handle').css('left', '70px');
	   $('.stop').click(function() {
                 play_pause(bid);
                 $('.stop').hide();
                 $('.stops').show();
	   });
	   $('.stops').click(function() {
                 play_play(bid);
                 $('.stops').hide();
                 $('.stop').show();
	   });
           get_data(data_id,1);
           if(Music.length>0){
                 player(bid);
           }
           ieplay();
     });

}else{

     $(function(){
	$("#player").jPlayer({
		ready:function (event){
                        get_data(data_id,1);
                        if(Music.length>0){
                            player(bid);
                        }
		},
		play:function(event)
		{
                        $("#lists_"+bid).removeClass('paused');
                        if(type==0){
                             $("#history").removeClass('paused');
                        }
                        if(type==1){
                             $("#favor").removeClass('paused');
                        }
                        if(type==2){
                             $("#rand").removeClass('paused');
                        }
			$("#tpic").attr('src',Music[bid]['tpic']);
			$("#musicname").html(Music[bid]['name']);
			$("#singer").attr('href',Music[bid]['singerlink']);
			$("#singer").html(Music[bid]['singer']);
			$("#topic").attr('href',Music[bid]['topiclink']);
			$("#topic").html(Music[bid]['topic']);
		},
		pause:function(event)
		{
                        if(type==0){
                             $("#history").addClass('paused');
                        }
                        if(type==1){
                             $("#favor").addClass('paused');
                        }
                        if(type==2){
                             $("#rand").addClass('paused');
                        }
                        $("#lists_"+bid).addClass('paused');
		},
		ended:function(event)
		{
                        play_next();
		},
		timeupdate:function(event)
		{
			var percent = (event.jPlayer.status.currentTime / event.jPlayer.status.duration) *100;
                        $('.js-player-position-slider').css('left',percent + "%");
                        $('.mb-info-wrap-default').hide();
                        $('.js-player-img-wrap').show();
		},
		supplied:"mp3,m4a",
		swfPath:web_url+"song/js",
		wmode:"window",
		loop:false,
		volume:1,
		cssSelectorAncestor: "",
		cssSelector:{
			currentTime:".t-now",
			duration:".t-total",
			seekBar:".js-player-loaded",
			playBar:".js-player-position",
			play: ".stops",
			pause: ".stop",
			mute: ".btn-umuted",
			unmute: ".btn-muted",
			volumeBar: ".js-player-volume-strip",
			volumeBarValue: ".vol-slider-handle-wrap"
		}
	});
     });
}
$(document).ready(function(){
	$(document).bind("click", function (e) {
    	        if($(e.target).closest(".search-box").length>0){
			$('#search-box').addClass('search-box-h');
		}else{
			$('#search-box').removeClass('search-box-h');
		}
	});
	$(".js-userInfo").hover(function(){
              $(this).addClass('active');
              $('.login-user-list').css('overflow','visible');
	}, function() {
              $(this).removeClass('active');
              $('.login-user-list').css('overflow','hidden');
        });
        var eTimer = null,lTimer = null,$navBtn = $('#share'),$sNavMenu = $('.tips-player');
        $navBtn.hover(function() {
                  if (lTimer) clearTimeout(lTimer);
                  eTimer = setTimeout(function () {
                       $('.mb-relate-wrap').css('z-index','2');
                       $sNavMenu.show();
                       $sNavMenu.css('top','24px');
                  }, 100);
        }, function() {
                  if (eTimer) clearTimeout(eTimer);
                  lTimer = setTimeout(function () {
                       $('.mb-relate-wrap').css('z-index','1');
                       $sNavMenu.hide();
                  }, 100)
        });
        $sNavMenu.hover(function() {
                  if (lTimer) clearTimeout(lTimer);
                  $('.mb-relate-wrap').css('z-index','2');
                  $sNavMenu.css('top','24px');
                  $(this).show();
        }, function() {
                  $('.mb-relate-wrap').css('z-index','1');
                  $(this).hide();
        });
	$('.lyric').click(function() {
              var none=$('.mb-lrc-wrap').css('display');
              if(none=='none'){
                  $('.mb-lrc-wrap').css('display','block');
                  $('.mb-song-list-wrap').removeAttr('style');
              }else{
                  $('.mb-lrc-wrap').css('display','none');
                  $('.mb-song-list-wrap').css('margin-right','20px');
              }
	});
	$('#search_kw').click(function() {
              $('#search-box').addClass('search-box-h');
	});
	$(".ui-slider-handle").hover(function(){
              $('.ui-slider').addClass('hover');
	}, function() {
              $('.ui-slider').removeClass('hover');
        });
	$('.prev').click(function() {
              play_up();
	});
	$('.next').click(function() {
              play_next();
	});
	$('.js-list-sel-all').click(function() {
              xuanall(3);
              var xuan=$(this).attr('xuan');
              if(xuan==0){
                     $(this).addClass('select-all-s');
                     $(this).attr('xuan','1');
              }else{
                     $(this).removeClass('select-all-s');
                     $(this).attr('xuan','0');
              }
	});
	$('.js-remove-list').click(function() {
              xuanall(1);
	});
	$('.btn-login-pop').click(function() {
              get_log();
	});
	$('.btn-logout-pop').click(function() {
              get_logout();
	});
	$('.pop-dia-close').click(function() {
              get_log_clock();
	});
	$('#history').click(function() {
              type=0;
              get_history();
	});
	$('#favor').click(function() {
              type=1;
              get_favor();
	});
	$('#rand').click(function() {
              type=2;
              get_rand();
	});
	$('.js-player-favor').click(function() {
              get_like(bid,2);
	});
	$('.js-player-mode').click(function() {
              var sidx=$(this).attr('sid');
              var sids=$('.js-player-mode-show').attr('sid');
              $('.js-player-mode-show').removeClass(sids);
              if(sxid==1){
                     $('.cycle a').removeClass("curr");
              }
              if(sxid==2){
                     $('.single a').removeClass("curr");
              }
              if(sxid==3){
                     $('.random a').removeClass("curr");
              }
              if(sidx==1){
                   $('.js-player-mode-show').addClass('btn-cycle'); 
                   $('.js-player-mode-show').attr('sid','btn-cycle');
              }
              if(sidx==2){
                   $('.js-player-mode-show').addClass('btn-single'); 
                   $('.js-player-mode-show').attr('sid','btn-single');
              }
              if(sidx==3){
                   $('.js-player-mode-show').addClass('btn-random'); 
                   $('.js-player-mode-show').attr('sid','btn-random');
              }
              $(this).addClass('curr'); 
              sxid=sidx;
	});
	$('.js-recommand-change').click(function() {
              get_cai(bid);
	});
	$('.js-open-share').click(function() {
              var url=$(this).attr('href');
              var url=url.replace('%id%',Music[bid]['id']);
              var url=url.replace('%pic%',Music[bid]['tpic']);
              var url=url.replace('%desc%',Music[bid]['name']);
              window.open(url);
              return false;
	});
        //关闭声音
	$('.btn-umuted').click(function() {
              if(isIE6){
                    try{document.getElementById('mediaPlayerObj').settings.volume=0;$('.btn-muted').show();$('.btn-umuted').hide();}catch(e){return(false);}
              }
	});
        //打开声音
	$('.btn-muted').click(function() {
              if(isIE6){
                    try{var d=$('.vol-slider-handle').attr('vol');document.getElementById('mediaPlayerObj').settings.volume=d+20;$('.btn-umuted').show();$('.btn-muted').hide();}catch(e){return(false);}
              }
	});
        //下载
	$('.js-player-dl').click(function() {
              window.open(Music[bid]['downlink'],'down');
              return false;
	});
});
/*IE6播放器状态检测*/
var buffering=0;
var stoped=0;
var linking=0;
var ready=0;
function ieplay(){
         var cscms=document.getElementById("mediaPlayerObj");
         var currentZt = cscms.playState;
         switch(currentZt){

	         case 10://准备就绪
		         ready+=2;
		         if(ready>=120){
			         ready=0;
			         play_next();
		         }
		         break;
	         case 9://正在连接
		         linking+=2;
		         if(linking>=480){
			         linking=0;
			         play_next();
		         }
		         break;
	         case 6://正在缓冲
		         buffering+=2;
		         if(buffering>=480){buffering=0;
			         play_next();
		         }
		         break;
	         case 3://正在播放
                         $('.mb-info-wrap-default').hide();
                         $('.js-player-img-wrap').show();
		         if(parseInt(cscms.controls.currentPosition)>1){stnumk=0;}
                         $("#lists_"+bid).removeClass('paused');
                         if(type==0){
                             $("#history").removeClass('paused');
                         }
                         if(type==1){
                             $("#favor").removeClass('paused');
                         }
                         if(type==2){
                             $("#rand").removeClass('paused');
                         }
			 $("#tpic").attr('src',Music[bid]['tpic']);
			 $("#musicname").html(Music[bid]['name']);
			 $("#singer").attr('href',Music[bid]['singerlink']);
			 $("#singer").html(Music[bid]['singer']);
			 $("#topic").attr('href',Music[bid]['topiclink']);
			 $("#topic").html(Music[bid]['topic']);
		         try {
			         $(".js-play-time-now").text(showPlayingTime(parseInt(cscms.controls.currentPosition)));
			         $(".js-play-time-total").text(showPlayingTime(parseInt(cscms.currentMedia.duration)));
			         var d=parseInt(parseInt(cscms.controls.currentPosition) / parseInt(cscms.currentMedia.duration)*100);

                                 var zwd=$('.js-player-setOpsition').css('width');
                                 var wd=$('.js-player-position').css('width');
                                 var jindu=parseInt(zwd)*(parseInt(cscms.controls.currentPosition)/parseInt(cscms.currentMedia.duration));
		                 if(parseInt(wd) < parseInt(zwd)){
      		         	         $('.js-player-position').css('width',jindu);
      		         	         $('.js-player-position-slider').css('left',jindu);
       		                 }

		         } catch (e) {}
		         buffering=linking=ready=stoped=0;
		         break;
	         case 2://暂停
                        if(type==0){
                             $("#history").addClass('paused');
                        }
                        if(type==1){
                             $("#favor").addClass('paused');
                        }
                        if(type==2){
                             $("#rand").addClass('paused');
                        }
                        $("#lists_"+bid).addClass('paused');
		         break;
	         case 1:	//播放完停止了
		         $('.js-player-loaded').css('width', '0px');
		         $('.js-player-position').css('width', '0px');
		         $('.js-player-position-slider').css('left', '0px');
		         play_next();
		         break;
	         default://其他
		         stoped+=2;
		         if(stoped>=480){stoped=0;
			         play_next();
		         }
		         break;
         }
         setTimeout("ieplay()", 200); 
}

function get_history(){
        $('#history').addClass('curr');
        $('#favor').removeClass('curr');
        $('#rand').removeClass('curr');
        get_data(data_id,0);
}
function get_favor(){
        $('#history').removeClass('curr');
        $('#favor').addClass('curr');
        $('#rand').removeClass('curr');
        if(Musicfavor.length==0){
              $.getJSON(cscms_path+"index.php/dance/playsong/favs?callback=?",function(data) {
                      if(data['error']=='login'){
                           get_log();
                      }else{
                         Music=data;
                         Musicfavor=data;
                         play_list();
                         if($('.mb-info-wrap-default').css('display')=='block'){
                             player(0);
                         }
		      }
	      });
        }else{
            Music=Musicfavor;
            play_list();
            $('#lists_'+bid).addClass('played');
        }
}
function get_rand(){
        $('#history').removeClass('curr');
        $('#favor').removeClass('curr');
        $('#rand').addClass('curr');
        if(Musicrand.length==0){
             $.getJSON(cscms_path+"index.php/dance/playsong/rand?callback=?",function(data) {
                   Music=data;
                   Musicrand=data;
                   play_list();
                   if($('.mb-info-wrap-default').css('display')=='block'){
                        player(0);
                   }
	     });
        }else{
            Music=Musicrand;
            play_list();
            $('#lists_'+bid).addClass('played');
        }
}
function get_data(_id,_sid){
        if(MusicList.length==0){
            $.getJSON(cscms_path+"index.php/dance/playsong/data?id=" + data_id+"&callback=?",function(data) {
		if (data) {
                   Music=data;
                   MusicList=data;
                   play_list();
                   if(_sid==1 && Music.length>0){
                         player(0);
                   }
		}else{
                   play_list();
                }
	    });
        }else{
            Music=MusicList;
            play_list();
            $('#lists_'+bid).addClass('played');
        }
}
function xuan(bid){
        var xuan=$('#lists_'+bid).attr('xuan');
        if(xuan==0){
              $('#lists_'+bid).addClass('selected');
              $('#lists_'+bid).attr('xuan','1');
        }else{
              $('#lists_'+bid).removeClass('selected');
              $('#lists_'+bid).attr('xuan','0');
        }
}
function play_list(){
        $('.js-play-list').html('');
        for (var i = 0; i < Music.length; i++) {
             var html='<li id="lists_'+i+'" data-id="'+Music[i]['id']+'" class="" xuan="0" bid="'+i+'"><span class="col song-name"><a href="javascript:xuan('+i+');" class="vc-btn btn-checkbox js-play-sel"><span class="vc-btn-inner">复选</span></a><i class="ico-play"></i><a class="col-inner js-list-name" hidefocus="true" href="javascript:player('+i+');" title="'+Music[i]['name']+'">'+Music[i]['name']+'</a></span><span class="col song-oper"><a href="javascript:player('+i+');" hidefocus="true" title="播放" class="vc-btn btn-play js-play-btn"><span class="vc-btn-inner">播放</span></a>';
             var htmls=html+'<a href="javascript:play_pause('+bid+');" hidefocus="true" title="暂停" class="vc-btn btn-stop js-pause-btn"><span class="vc-btn-inner">暂停</span></a><a id="like_'+i+'" href="javascript:get_like('+i+',1);" hidefocus="true" title="喜欢" class="vc-btn btn-love js-add-favor "><span class="vc-btn-inner">喜欢</span></a><a href="javascript:play_del('+i+');" hidefocus="true" title="删除" class="vc-btn btn-del js-del-btn"><span class="vc-btn-inner">删除</span></a>&nbsp;</span><span class="col song-singer"><a href="'+Music[i]['singerlink']+'" target="_blank" class="col-inner" title="'+Music[i]['singer']+'">'+Music[i]['singer']+'</a></span><span class="col song-album"><a href="'+Music[i]['topiclink']+'" title="'+Music[i]['topic']+'" target="_blank" class="col-inner">'+Music[i]['topic']+'</a></span><span class="col song-time"><span class="col-inner">'+Music[i]['time']+'</span></span></li>';
             $('.js-play-list').append(htmls);
        }
        if(Music.length==0){
             $('.js-tab-list').show();
             $('.js-songlist-oper').hide();
        }else{
             $('.js-tab-list').hide();
             $('.js-songlist-oper').show();
        }
        if(type==0){
            $('.js-history-num').html('('+Music.length+')');
        }
}
function player(n){
        $song_Lrc[0] = '';
        $('#lyric').hide();
        $('.mb-oper').show();
        $('.js-lrc-no').hide();
        $('.js-lrc-load').show();
        for (var i = 0; i < 20; i++) {
              $('#LR'+i).html('');
        }
        if($('.js-recommand-list').html()==''){
            get_cai(bid);
        }
        $('#lists_'+bid).removeClass('played');
        $('#lists_'+bid).removeClass('paused');
        bid=n;
        $('#lists_'+bid).addClass('played');
        var firstplay=Music[bid]['url'];
        did=Music[bid]['id'];
	if(isIE6){
		document.getElementById("mediaPlayerObj").URL = firstplay; 
                try{document.getElementById('mediaPlayerObj').controls.play();}catch(e){return(false);}
	}else{
	        if(firstplay.indexOf(".m4a")>0){
		        $("#player").jPlayer("setMedia", {m4a:firstplay}).jPlayer("play");
	        }else{
			$("#player").jPlayer("setMedia", {mp3:firstplay}).jPlayer("play");
		}
	}
        document.title = Music[bid]['name'];
	pu.downloadlrc(0, Music[bid]['lrc']);
	pu.PlayLrc(0);
        if(Music[bid]['topic']=='-'){
              $('.albumInfo').hide();
        }else{
              $('.albumInfo .songTitle').html(Music[bid]['name']);
              $('.albumInfo img').attr('src',Music[bid]['tpic']);
              $('.albumInfo').show();
        }
        if(type==0){
              $("#history").addClass('played');
              $("#favor").removeClass('played');
              $("#rand").removeClass('played');
        }
        if(type==1){
              $("#history").removeClass('played');
              $("#favor").addClass('played');
              $("#rand").removeClass('played');
        }
        if(type==2){
              $("#history").removeClass('played');
              $("#favor").removeClass('played');
              $("#rand").addClass('played');
        }
        $.getJSON(cscms_path+"index.php/dance/playsong/rand?callback=?");
	$.ajax({type: "GET",url: cscms_path+'index.php/dance/hits/ids/' + Music[bid]['id']});
}
function play_play(n){
        if(isIE6){
               try{
                   document.getElementById('mediaPlayerObj').controls.play();
               }catch(e){
                   return(false);
               }
        }else{
               $("#player").jPlayer("play");
        }
}
function play_pause(n){
        if(isIE6){
               try{
                   document.getElementById('mediaPlayerObj').controls.pause();
               }catch(e){
                   return(false);
               }
        }else{
               $("#player").jPlayer("pause");
        }
}
function play_up(){
        var sid=bid-1;
        if(sid<0){
             var sid=Music.length-1;
        }
        player(sid);
}
function play_next(){
        var xid=bid+1;
        if(Music.length-1<xid){
               var xid=0;
        }
        if(sxid==2){  //单曲循环
            var xid=bid;
        }
        if(sxid==3){  //随机播放
            var xid=GetRandomNum(0,(Music.length-1));
        }
        player(xid);
}
function play_del(_id) {
	if (bid != _id) {
	     Music.splice(_id, 1);
             play_list();
             for (var i = 0; i < Music.length; i++) {
                  var dids=$("#lists_"+i).attr('data-id');
                  if(did==dids){
                      var pids=$("#lists_"+i).attr('bid');
                      $('#lists_'+pids).addClass('played');
                      break;
                  }
             }
	     $('.js-history-num').html('('+Music.length+')');
	     $('#lists_'+_id).hide();
             if(type==0){
                   MusicList=Music;
             }
             if(type==1){
                   Musicfavor=Music;
             }
             if(type==2){
                   Musicrand=Music;
             }
	}
}
function xuanall(xsid){
       var v = [];
　　   var a=$(".js-play-list li");  
       for (var i = 0; i < a.length; i++) {
              if(xsid==3){
                     xuan(i);
              }
              if(xsid==2){
                 var xuanid=$("#lists_"+i).attr('xuan');
                 var bfid=$("#lists_"+i).attr('bid');
                 if(xuanid==1){
                     play_add(bfid);
                 }
              }
              if(xsid==1){
    
                 var xuanid=$("#lists_"+i).attr('xuan');
                 var bfid=$("#lists_"+i).attr('bid');
                 if(xuanid==1){
                     	if (bfid != bid) {
				v.push(bfid);
			}
                 }
              }
       }
       if(xsid==1){
             Music = removeBatch2(Music, v);
             play_list();
             $('.js-play-list').find('li').removeClass('played').eq(bid).addClass('played');
             $('.js-list-sel-all').removeClass('select-all-s');
       }
}
function removeBatch2(arr, toDeleteIndexes) {
	toDeleteIndexes.sort(function compare(a, b) {
		return b - a;
	});
	for (var i = 0; i < toDeleteIndexes.length; i++) {
		arr.splice(toDeleteIndexes[i], 1);
	}
        if(type==0){
             MusicList=arr;
        }
        if(type==1){
             Musicfavor=arr;
        }
        if(type==2){
             Musicrand=arr;
        }
	return arr;
}
function get_like(_id,_sid){
       var did=Music[_id]['id'];
       $.getJSON(cscms_path+"index.php/dance/playsong/fav_add?id=" + did+"&callback=?",function(d) {
              var data=d['str'];
              if(data=='login'){
                    get_log();
              } else if(data=='ok'){
                    if(_sid==1){
                       $('#like_'+_id).addClass('btn-loved');
                    }else{
                       $('.js-player-favor').addClass('loved');
                    }
                    $('.js-favor-anim').addClass('loved-anim');
                    setTimeout('get_xiaoguo()', 3000);
                    Musicfavor=[];
              } else if(data=='del'){
                    if(_sid==1){
                       $('#like_'+_id).removeClass('btn-loved');
                    }else{
                       $('.js-player-favor').removeClass('loved');
                    }
                    Musicfavor=[];
              } else {
                    alert(data);
              }
       });
}
function get_xiaoguo(){
       $('.js-favor-anim').removeClass('loved-anim');
}
function get_log(){
       $('#error_tips').html('');
       $('#error_tips').show();
       $('#pop_mask_bg').show();
       $('#mod_quc_pop').show();
}
function get_log_clock(){
       $('#error_tips').hide();
       $('#pop_mask_bg').hide();
       $('#mod_quc_pop').hide();
       $('#cscms_pass').val('');
}
function get_islog(){
       $.getJSON(cscms_path+"index.php/dance/playsong/log?random="+Math.random()+"&callback=?",function(data) {
              if(data['error']=='no'){
                  $('.nloginWrap').show();
                  $('.loginWrap').hide();
              } else {
                  $('.nloginWrap').hide();
                  $('.loginWrap').show();
                  $('.popUsername').html(data['nichen']);
                  $('.js-userInfo-favor').html(data['favnums']);
                  $('.js-userInfo-favor-tl').attr('title','喜欢了'+data['favnums']+'首歌曲');
              }
       });
}
function get_cai(_id){
       $('.js-recommand-list').html('');
       $.getJSON(cscms_path+"index.php/dance/playsong/cais?id="+Music[_id]['id']+"&callback=?",function(data) {
              if(data){
                  Musiccai=data;
                  $('.js-recommand-wrap').show();
                  for (var i = 0; i < data.length; i++) {
                          var html='<li class="hack-hover"><div class="list-inner"><span class="col song-name"><a href="javascript:caiplay('+i+',1);" class="js-360-play" title="'+data[i]['name']+'">'+data[i]['name']+'</a></span><span class="col song-singer" title="'+data[i]['singer']+'">'+data[i]['singer']+'</span><span class="col song-oper"><a href="javascript:caiplay('+i+',1);" class="icon-icomoon js-cscms-play" title="播放"></a><a href="javascript:caiplay('+i+',2);" class="icon-icomoon js-cscms-add" title="添加"></a></span></div></li>';
                          $('.js-recommand-list').append(html);
                  }
              } else {
                  alert('网络连接失败！');
              }
       });
}
function caiplay(_id,_sid){
       if(_sid==3){
             for (var i = 0; i <Musiccai.length; i++) {
                     Music.unshift(Musiccai[i]);
             }
             play_list();
             player(0);
       }else{
             Music.unshift(Musiccai[_id]);
             play_list();
             if(_sid==1){
                 player(0);
             } else {
                 $('#lists_'+bid).addClass('played');
             }
       }
}

function get_login(){
           $.getJSON(cscms_path+"index.php/api/ulog/login?username="+$('#cscms_name').val()+"&userpass="+$('#cscms_pass').val()+"&callback=?",function(data) {
               if(data['error']=='10001'){ //用户名为空
                    $('#error_tips').html('帐号不能为空!');
               } else if(data['error']=='10002'){ //密码为空
                    $('#error_tips').html('密码不能为空!');
               } else if(data['error']=='10003'){ //帐号不存在
                    $('#error_tips').html('您的帐号不存在!');
               } else if(data['error']=='10004'){ //密码错误
                    $('#error_tips').html('您的密码错误!');
               } else if(data['error']=='10005'){ //帐号被锁定
                    $('#error_tips').html('您的帐号被锁定!');
               } else if(data['error']=='10006'){ //登入成功
                    get_log_clock();
                    get_islog();
               } else if(data['error']=='10007'){ //邮件未激活
                    $('#error_tips').html('您的帐号未激活，请去邮箱激活!');
               } else { 
                   $('#error_tips').html(data['error']);
               }
           });
}
function get_logout(){
     $.getJSON(cscms_path+"index.php/api/ulog/logout?callback=?",function(data) {
           if(data['error']=='10001'){
                get_islog();
           } else {
                do_alert('网络故障，连接失败!');
           }
     });
}
function GetRandomNum(Min,Max){ 
        var Range = Max - Min; 
        var Rand = Math.random(); 
        return(Min + Math.round(Rand * Range)); 
} 
function showPlayingTime(seconds){
        var minute=parseInt(seconds/60);
        var second=parseInt(seconds-minute*60);
        if(minute<10){minute='0'+minute;}if(second<10){second='0'+second;}
        return minute+':'+second;
}
function reinitIframe(){
   try{
         var mainheight=$(window).height();
         var xheight2=mainheight-195;
         var playeryl=$('.vol-slider-handle-wrap').css('width');
         var topnone=$('.albumInfo').css('display');
         if(topnone=='block'){
               var xheight3=mainheight-310;
         }else{
               var xheight3=mainheight-160;
         }
         $('.js-player-volume-slider').css('left',playeryl);
         $('.song-list').css('height',xheight2+'px');
         $('.lrc-block-wrap').css('height',xheight3+'px');

   }catch(ex){}
}
window.setInterval("reinitIframe()",200);
get_islog();
