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
var so = {
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
