
function $Showhtml(){
       if(window.ActiveXObject || window.ActiveXObject !== undefined)
            player = $PlayerIe();
       else
            player = $PlayerNt();

       document.getElementById('playlist').innerHTML = player;
}

function $PlayerNt(){
	if (navigator.plugins) {
            var Install = false;
				for (i=0; i < navigator.plugins.length; i++ ) 
				{
					var n = navigator.plugins[i].name;
					if( navigator.plugins[n][0]['type'] == 'application/xfplay-plugin')
					{
						Install = true; break;
					}		
				} 

		if(Install){
			player = '<embed type="application/xfplay-plugin" PARAM_URL="'+parent.cs_url+'" PARAM_Status="1" width="100%" height="'+parent.cs_height+'" id="Xfplay" name="Xfplay"></embed>';
			return player;
		}
	}
	$YyxfInstall();
}

function $PlayerIe(){
         player = '<object ID="Xfplay" name="Xfplay" width="100%" height="'+parent.cs_height+'" onerror="$YyxfInstall();" classid="clsid:E38F2429-07FE-464A-9DF6-C14EF88117DD" >';
         player += '<PARAM name="URL" value="'+parent.cs_url+'">';
         player += '<PARAM name="Status" value="1"></object>';
         return player;
}

if(parent.cs_adloadtime){
	setTimeout("$Showhtml();",parent.cs_adloadtime*1000);
}
