var pid=1;
var pg=1;
var lid=1;
function choose(n){
        getcls(n);
        var pic=$('#pic-'+n).attr("src");
        var text=$('#txt-'+n).attr("data-txt");
        var k=$('#txt-'+n).attr("data-i");
        $('#bigPic').attr('src',pic);
        $('#downItem').attr('href',pic);
        $('#photoNavIndex').text(k);
        $('.tips p').html(text);
        pid=n;
}
function prevPic(){
    if(pid>1){
       if(pg>1){
           prevGroup();
       }else{
           var n=pid-1;
           getcls(n);
           var pic=$('#pic-'+n).attr("src");
           var text=$('#txt-'+n).attr("data-txt");
           var k=$('#txt-'+n).attr("data-i");
           $('#bigPic').attr('src',pic);
           $('#downItem').attr('href',pic);
           $('#photoNavIndex').text(k);
           $('.tips p').html(text);
           pid=n;
       }
    }
}
function nextPic(){
    var psid=pg*7;
    if(pid==psid){
        nextGroup();
    }else{
        var n=pid+1;
        if ($("#pic-"+n).length > 0 ) {
           getcls(n);
           var pic=$('#pic-'+n).attr("src");
           var text=$('#txt-'+n).attr("data-txt");
           var k=$('#txt-'+n).attr("data-i");
           $('#bigPic').attr('src',pic);
           $('#downItem').attr('href',pic);
           $('.tips p').html(text);
           $('#photoNavIndex').text(k);
           pid=n;
        }else{
           $('#addiv').show(); //打开广告
        }
    }
}
function prevGroup(){
    var sid=lid;
    lid=(pg-2)*7;
    if(lid==0){
         lid=1;
         pid=1;
    }else{
         pid=lid+1;
    }
    if ( $("#lists_"+lid).length > 0 ) {
         $("#lists_"+sid).hide();
         $("#lists_"+lid).show();
         pg--;
         choose(pid);
    }else{
         if(lid<1){
            lid=1;
         }
         alert('已经是第一页了');
    }
}
function nextGroup(){
    var sid=lid;
    lid=pg*7;
    if ( $("#lists_"+lid).length > 0 ) {
         $("#lists_"+sid).hide();
         $("#lists_"+lid).show();
         pid=lid+1;
         pg++;
         choose(pid);
    }else{
         lid=sid;
         alert('已经是最后一页了');
    }
}
function getcls(n){
    var num=$(".pic_list li").length;
    for (i=1; i<=num; i++) {
        if(i==n){
           $('#txt-'+i).addClass("orange").removeClass("gray");
        }else{
           $('#txt-'+i).removeClass("orange").addClass("gray");
        }
    }
}

//顶踩视频 
function pic_ding(_ac,_id){
     $.getJSON(cscms_path+"index.php/pic/ajax/picding/"+_ac+"/"+_id+"?callback=?",function(data) {
           if(data){
               if(data['msg']=='ok'){
                   if(_ac=='ding'){
                        $("#upCnt").text(parseInt($("#upCnt" ).text()) + 1);
                   }else{
                        $("#downCnt").text(parseInt($("#downCnt" ).text()) + 1);
                   }
               }else{
                   do_alert(data['msg']);
               }
           } else {
                do_alert('网络故障，连接失败!');
           }
     });
}
function closead(){
     $('#addiv').hide();
}

