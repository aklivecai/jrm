<?php
Tak::regScript('end', '
if(window.opener == undefined) {
	window.opener = window.dialogArguments;
}   
    window.opener.popupCate(data.node);
    window.close();	
');
