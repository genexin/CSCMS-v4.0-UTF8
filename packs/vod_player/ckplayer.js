function $Showhtml(){
    document.getElementById('playad').style.display = "none";
    var playhtml='<embed wmode="opaque" src="'+parent.cs_root+'packs/vod_player/ckplayer/ckplayer.swf" flashvars="f='+unescape(url)+'&my_url='+encodeURIComponent(window.location.href)+'" quality="high" width="100%" height="'+height+'" align="middle" allowScriptAccess="always" allowFullscreen="true" type="application/x-shockwave-flash"></embed>';
    document.getElementById('playlist').innerHTML = playhtml;
}
if(parent.cs_adloadtime){
	setTimeout("$Showhtml();",parent.cs_adloadtime*1000);
}
