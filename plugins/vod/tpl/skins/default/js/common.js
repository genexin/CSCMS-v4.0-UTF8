var hadpingfen=0;
var pingfen = {
	init: function(){
	    $("ul.rating li").hover(function(){
		var title = $(this).attr("title");
		$(this).nextAll().removeClass("active");
		$(this).prevAll().addClass("active");
		$(this).addClass("active");
		$("#ratewords").html(title);
	    },function(){
		var num = $(this).index();
		var n = num+1;	
		$("ul.rating li:lt("+n+")").removeClass("hover");
	    });
	    $("ul.rating li").click(function(){
		var fen = $(this).attr("val");
                vod_pfen(fen);
	    });
	}
}
//获取评分
function vod_fen(_id){
     $.getJSON(cscms_path+"index.php/vod/ajax/vodpfen/"+_id+"?callback=?",function(data) {
          if(data){
                  if(data['fen']>8){
                      $('#fen_5').addClass("active");
                      $('#fen_4').addClass("active");
                      $('#fen_3').addClass("active");
                      $('#fen_2').addClass("active");
                      $('#fen_1').addClass("active");
                      $('#ratewords').text("力荐");
                  } else if(data['fen']>6){
                      $('#fen_4').addClass("active");
                      $('#fen_3').addClass("active");
                      $('#fen_2').addClass("active");
                      $('#fen_1').addClass("active");
                      $('#ratewords').text("推荐");
                  } else if(data['fen']>4){
                      $('#fen_3').addClass("active");
                      $('#fen_2').addClass("active");
                      $('#fen_1').addClass("active");
                      $('#ratewords').text("还行");
                  } else if(data['fen']>2){
                      $('#fen_2').addClass("active");
                      $('#fen_1').addClass("active");
                      $('#ratewords').text("较差");
                  } else {
                      $('#fen_1').addClass("active");
                      $('#ratewords').text("很差");
                  }
           } else {
                  do_alert('网络故障，连接失败!');
           }
     });
}
//视频评分
function vod_pfen(_fen){
     var did=$('#detail-rating').attr('vod_id');
     if(hadpingfen==1){
                do_alert('您已经评过分了!');
     }else{
          $.getJSON(cscms_path+"index.php/vod/ajax/vodpfenadd/"+did+"/"+_fen+"?callback=?",function(data) {
              if(data){
                  if(data['msg']=='ok'){
                      do_alert('评分成功，感谢您的支持~!',2);
                      hadpingfen=1;
                  }else{
                      do_alert(data['msg']);
                  }
              } else {
                  do_alert('网络故障，连接失败!');
              }
           });
     }
}
//开关灯
function light(){
        var str=$('.light_switch span').html();
	if(str=='开灯'){
                $('.light_switch span').html('关灯');
                $('.light_switch span').css('color','#111');
                $('.light_switch').css('z-index','999');
		$('.playshow_mask').hide();
		$('.player').css("position","");
		$('.player').css("width","");
		$('.player').css("top","");
                $('.player').css("z-index","");
	}else{
                $('.light_switch span').html('开灯');
                $('.light_switch span').css('color','#ddd');
                $('.light_switch').css('z-index','1002');
                $('.playshow_mask').show();
		$('.player').css("position","absolute");
		$('.player').css("width","980px");
		$('.player').css("top","90px");
                $('.player').css("z-index","1002");
	}
}
//栏目切换
function play_cmd(n){
	for (i=1; i<5; i++) {
        if(i==n){
              $('#t_'+i).addClass('cur');
              $('#s_'+i).show();
		}else{
              $('#t_'+i).removeClass('cur');
              $('#s_'+i).hide();
		}
	}
}
//顶视频 
function vod_ding(id){
     $.getJSON(cscms_path+"index.php/vod/ajax/vodding/"+id+"?callback=?",function(data) {
           if(data){
               if(data['msg']=='ok'){
                   $("#dhits").text(parseInt($("#dhits" ).text()) + 1);
               }else{
                   do_alert(data['msg'],2);
               }
           } else {
                do_alert('网络故障，连接失败!');
           }
     });
}
//收藏视频 
function vod_fav(id){
     $.getJSON(cscms_path+"index.php/vod/ajax/vodfav/"+id+"?callback=?",function(data) {
           if(data){
               if(data['msg']=='ok'){
                   $("#shits").text(parseInt($("#shits" ).text()) + 1);
               }else{
                   do_alert(data['msg'],2);
               }
           } else {
                do_alert('网络故障，连接失败!');
           }
     });

}
