var arr = new Array();
var pid=did=0;
var audio;
var waps = {
	init: function(){
          $('.songslist .btn_play').click(function () {
               var id=$(this).attr('did');
               if(audio && id==pid){
                    plays();
               }else{
                    wap_player(id);
               }
          });
          $('#btn_main').click(function () {
               plays();
          });
          $('#btn_next').click(function () {
               play_next();
          });
          //歌词切换
          $('#btn_change').click(function () {
               var nones=document.getElementById("cover").style.display;
               if(nones=='none'){
                    $('#btn_change').removeClass("btn_lrc").addClass("btn_cover");
                    $('.cover').show();
                    $('.lrc').hide();
               }else{
                    $('#btn_change').removeClass("btn_cover").addClass("btn_lrc");
                    $('.cover').hide();
                    $('.lrc').show();
               }
          });
          $('#btn_mainplay').click(function () {
               play_one();
          });
	}
}

function wap_player(n) { 
     if(audio){
         audio.pause();
     }
     $('#mod_player').show();
     if(pid>0){
         $('#player_'+pid+' .btn_play').removeClass("play").addClass("pause");
     }
     var dance= $('#player_'+n).attr('data-str');
     arr = dance.split("==cscms==");
     name=arr[0];
     did=arr[1];
     url=cscms_path+'index.php/dance/play/url/wap/'+did+'.mp3';
     audio = $("#audio")[0];
     audio.src = url;
     $('#songname').html(name);
     pid=n;
     plays();
}

function wap_player_one() { 
     url=cscms_path+'index.php/dance/play/url/wap/'+mp3_id+'.mp3';
     audio = $("#audio")[0];
     audio.src = url;
     pu.downloadlrc(0);
     pu.PlayLrc(0);
}

function play_one(){
    if(audio){
        if(!audio.paused){
            audio.pause();
            $('#btn_mainplay').removeClass("btn_pause").addClass("btn_play");
        }else{
            audio.play();
            $('#btn_mainplay').removeClass("btn_play").addClass("btn_pause");
        }
        setInterval('ptimes()',500);
    }else{
        alert('您还没有选择歌曲');
    }
}

function plays(){
    if(audio){
        if(!audio.paused){
            $('#player_'+pid+' .btn_play').removeClass("play").addClass("pause");
            $('#btn_main').removeClass("btn_pause").addClass("btn_play");
            audio.pause();
        }else{
            $('#player_'+pid+' .btn_play').removeClass("pause").addClass("play");
            $('#btn_main').removeClass("btn_play").addClass("btn_pause");
            audio.play();
        }
        setInterval('ptimes()',1000);
    }else{
        alert('您还没有选择歌曲');
    }
}

function play_next(){
    var dancecount=$(".dance_list li").length; 
    if(audio){
        audio.pause();
    }
    var xid=Number(pid)+1;
    if(xid > dancecount){
       xid=1;
    }
    wap_player(xid);
}

function ptimes(){
    if(audio.ended){ //播放完成
        if(typeof(mp3_lrc)=="undefined"){
            play_next();
        }else{
            play_one();
        }
    } else {
        totalTime=formatSeconds(audio.duration);
        mpetime=formatSeconds(audio.currentTime);
        if(typeof(mp3_lrc)=="undefined"){
        }else{
             $('#timeshow').html(mpetime);
             $('#time').html(totalTime);
	     //播放进度条
             var w=$('#progress_wrap').css('width');
	     var progressValue = audio.currentTime/audio.duration*parseInt(w);
	     $('#progress').css('width',parseInt(progressValue) + 'px');
        }
    }
}

function formatSeconds(value) {
   if(value=='NaN'){
       value=0;
   }
   var theTime = parseInt(value);// 秒
   var theTime1 = 0;// 分
   var theTime2 = 0;// 小时
   if(theTime > 60) {
      theTime1 = parseInt(theTime/60);
      theTime = parseInt(theTime%60);
      if(theTime1 > 60) {
         theTime2 = parseInt(theTime1/60);
         theTime1 = parseInt(theTime1%60);
       }
   }
   shi=parseInt(theTime2);
   fen=parseInt(theTime1);
   miao=parseInt(theTime);
   if(shi > 0){
       fen=shi*60+fen;
   }
   if(miao < 10){
         miao = "0"+miao;
   }
   if(fen < 10){
         fen = "0"+fen;
   }
   result=fen+":"+miao;
   return result;
}



