//首页专辑切换
var topic = {
	init: function(){
	    $("#topic_1").hover(function(){
                  $('#topic_1').addClass('selected');
                  $('#topic_2').removeClass('selected');
                  $('#topics_1').show();
                  $('#topics_2').hide();
	    },function(){});
	    $("#topic_2").hover(function(){
                  $('#topic_2').addClass('selected');
                  $('#topic_1').removeClass('selected');
                  $('#topics_2').show();
                  $('#topics_1').hide();
	    },function(){});
	    $('p[rel=htop5small]').hover(function(){
		  var idx=$(this).attr('oindex');
		  idx=Math.floor(idx);
		  for(var i=1;i<11;i++){
			if(i!=idx){
				$('#hbigDiv'+i).addClass('untxt');
				$('#hsmallDiv'+i).removeClass('untxt');
			}else{
				$('#hbigDiv'+i).removeClass('untxt');
				$('#hsmallDiv'+i).addClass('untxt');
			}
		  }
	    });
            $('#neir1').click(function(){
                  $('#neir2').removeClass('untxt');
                  $('#neir1').addClass('untxt');
                  $('#moreContent').css('height','auto');
	    });
            $('#neir2').click(function(){
                  $('#neir1').removeClass('untxt');
                  $('#neir2').addClass('untxt');
                  $('#moreContent').css('height','64px');
	    });
	}
}
//歌曲播放
var dance = {
	play: function(){
            //分享开关
            $('#dance_share').click(function(){
                   $('#showbg').css({width:$(window).width(),height:document.body.scrollHeight});
                   $('#showbg').show(); 
                   $('.popup').show();     
	    });
            //关闭分享
            $('.fancybox-close').click(function(){
                   $('#showbg').css({width:0,height:0});
                   $('#showbg').hide(); 
                   $('.popup').hide();     
	    });
            //分享
            $('.share li').click(function(){
                   var title=$('#showbg').attr('data-title');
                   var url=$('#showbg').attr('data-url');
                   var ac=$(this).attr('class');
                   dance_share(ac,title,url);
	    });
        }
}
//首页歌曲全部播放
function playsong(n){
        var v = [];
        var nums=$('.tab_div #song-'+n).length;
        for (var i = 0; i < nums; i++) {
              var did=$('.tab_div #song-'+n+':eq('+i+')').attr('did');
              v.push(did);
        }
        window.open(cscms_path+'index.php/dance/playsong?id=' + v.join(','), 'play');
}
//列表歌曲全部播放
function playsongs(n){
        var v = [];
　　    var a=$("input[name='check']"); 
        for (var i = 0; i < a.length; i++) {
            if(n==2){
                if(a[i].checked==true){
                    var did=a[i].value;
                    v.push(did);
                }
            }else{
                var did=a[i].value;
                v.push(did);
            }
        }
        if (1 > v.length){ 
             do_alert('请选择要播放的歌曲！');return; 
        }else{
             window.open(cscms_path+'index.php/dance/playsong?id=' + v.join(','), 'play');
        }
}
//全选、反选
function checkAll(){
        var v = [];
　　    var a=$("input[name='check']"); 
        for (var i = 0; i < a.length; i++) {
            if(a[i].checked==true){a[i].checked="";}else{ a[i].checked="checked";}
        }
}
//顶歌曲 
function dance_ding(id){
     $.getJSON(cscms_path+"index.php/dance/ajax/danceding/"+id+"?callback=?",function(data) {
           if(data){
               if(data['msg']=='ok'){
                   $("#dhits").text(parseInt($("#dhits" ).text()) + 1);
                   do_alert('感谢您的支持!',2);
               }else{
                   do_alert(data['msg']);
               }
           } else {
                do_alert('网络故障，连接失败!');
           }
     });
}
//收藏歌曲 
function dance_fav(id){
     $.getJSON(cscms_path+"index.php/dance/ajax/dancefav/"+id+"?callback=?",function(data) {
           if(data){
               if(data['msg']=='ok'){
                   $("#shits").text(parseInt($("#shits" ).text()) + 1);
                   do_alert('歌曲收藏成功!',2);
               }else{
                   do_alert(data['msg']);
               }
           } else {
                do_alert('网络故障，连接失败!');
           }
     });
}
//收藏专辑 
function album_fav(id){
     $.getJSON(cscms_path+"index.php/dance/ajax/albumfav/"+id+"?callback=?",function(data) {
           if(data){
               if(data['msg']=='ok'){
                   $("#shits").text(parseInt($("#shits" ).text()) + 1);
                   do_alert('专辑收藏成功!',2);
               }else{
                   do_alert(data['msg']);
               }
           } else {
                do_alert('网络故障，连接失败!');
           }
     });
}
function play_hits_tags(_id){
      for(var i=1;i<=3;i++){
            var curC=document.getElementById("play_tags_"+i);
            var curB=document.getElementById("play_list_"+i);
            if(_id==i){
                curB.style.display="block";
                curC.className="song_p_nav_clo"
            }else{
                curB.style.display="none";
                curC.className=""
            }
      }
}

function play_reco_tag_top(_id){
      for(var i=1;i<=3;i++){
            var curC=document.getElementById("reco_top_"+i);
            var curB=document.getElementById("reco_list_"+i);
            if(_id==i){
                curB.style.display="block";
                curC.className="recommend_tit_clo"
            }else{
                curB.style.display="none";
                curC.className=""
            }
      }
}

function play_reco_tag(_id,_n){
      if(_n==1){
           if(_id==1){
               var id=3;
           }else{
               var id=_id-1;
           }
      }
      if(_n==2){
           if(_id==3){
               var id=1;
           }else{
               var id=_id+1;
           }
      }
      for(var i=1;i<=3;i++){
            var curC=document.getElementById("reco_top_"+i);
            var curB=document.getElementById("reco_list_"+i);
            if(id==i){
                curB.style.display="block";
                curC.className="recommend_tit_clo"
            }else{
                curB.style.display="none";
                curC.className=""
            }
      }
}
//弹出分享
function showShareMusic(e, name, pic, link) {
    var show = $("#fsId").css("display");
    if (show == 'none') {
        var x = e.offsetLeft;
        var y = e.offsetTop;
        while (e = e.offsetParent) {
            x += e.offsetLeft;
            y += e.offsetTop;
        }
        $("#fsId").css("top",((y - 30) - jQuery('#checkALL').scrollTop()) + 'px');
        $("#fsId").css("left",(x - 180) + 'px');
        $("#fsId").css("display" ,'block');
        var title = "听到这首《" + name + "》，就无法淡定了……一定要转给大家一起听一听！(分享自@cscms)。点击收听：";
        try {
           var bdObj = $('.fenmiddle #bdshare');
           bdObj.attr("data", "{'comment':'" + title + "','text':'" + title + "','pic':'" + pic + "','url':'" + link + "'}");
        } catch(e) {}
    }
    if (show == 'block') {
        $("#fsId").css("display","none");
    }
}
//播放页分享
function dance_share(ac,title,url) {
	if(ac=='share_qzone'){
               var url="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url="+encodeURI(url)+"&title="+encodeURI('分享一首好听的音乐，--《'+title+'》播放地址：');
        } else if(ac=='share_qwei'){
               var url="http://share.v.t.qq.com/index.php?c=share&a=index&appkey=&url="+encodeURI(url)+"&title="+encodeURI('分享一首好听的音乐，--《'+title+'》播放地址：')+"&site=";
        } else if(ac=='share_weibo'){
               var url="http://service.weibo.com/share/share.php?appkey=&title="+encodeURI('分享一首好听的音乐，--《'+title+'》播放地址：')+"&url="+encodeURI(url);
        } else if(ac=='share_baidu'){
               var url="http://tieba.baidu.com/f/commit/share/openShareApi?url="+encodeURI(url)+"&title="+encodeURI('分享一首好听的音乐，--《'+title+'》播放地址：'+url)+"&desc="+encodeURI('分享一首好听的音乐，--《'+title+'》播放地址：'+url)+"&comment="+encodeURI('分享一首好听的音乐，--《'+title+'》播放地址：'+url);
        } else if(ac=='share_renren'){
               var url="http://widget.renren.com/dialog/share?resourceUrl="+encodeURI(url)+"&title="+encodeURI(title)+"&description="+encodeURI('分享一首好听的音乐，--《'+title+'》播放地址：'+url)+"&srcUrl="+encodeURI(url);
        } else if(ac=='share_kaixin'){
               var url="http://www.kaixin001.com/rest/records.php?url="+encodeURI(url)+"&style=11&content="+encodeURI('分享一首好听的音乐，--《'+title+'》播放地址：')+"&stime=&sig=";
	};
	window.open(url,'share');
}
