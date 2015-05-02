function $Showhtml(){
    document.getElementById('playad').style.display = "none";
    player = '<embed src="'+unescape(parent.cs_url)+'" allowFullScreen="true" quality="high" width="100%" height="'+parent.cs_height+'" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>';
    document.getElementById('playlist').innerHTML = player;
}
if(parent.cs_adloadtime){
	setTimeout("$Showhtml();",parent.cs_adloadtime*1000);
}

