function $Showhtml(){
    document.getElementById('playad').style.display = "none";
    player = '<embed src="http://player.56.com/v_'+parent.cs_url+'.swf" type="application/x-shockwave-flash" width="100%" height="'+parent.cs_height+'" allowfullscreen="true" allownetworking="all" allowscriptaccess="always"></embed>';
    //player = '<embed type="application/x-shockwave-flash" src="http://www.56.com/flashApp/out.14.04.03.d.swf" id="Player" bgcolor="#FFFFFF" quality="high" allowfullscreen="true" allowNetworking="internal" allowscriptaccess="never" wmode="transparent" menu="false" always="false"  pluginspage="http://www.macromedia.com/go/getflashplayer" width="100%" height="'+parent.cs_height+'" flashvars="vid='+parent.cs_url+'">';
    document.getElementById('playlist').innerHTML = player;
}
if(parent.cs_adloadtime){
	setTimeout("$Showhtml();",parent.cs_adloadtime*1000);
}

