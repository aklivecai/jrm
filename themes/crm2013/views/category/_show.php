<?php
	$this->regCssFile('jstree/default/style.min.css')
		->regScriptFile('plugins/jstree/jstree.min.js');

$data = Category::getCatsProduct();
$tags = array();
foreach ($data as $key => $value) {
    $state = array();
        $state['opened'] = true;
    if ($key == $id) {
        $state['selected'] = true;
    }
    $tags[] = array(
                    'id'=>$key,
                    'parent'=>($value['parentid']?$value['parentid']:'#'),
                    'text'=>$value['catename'],
                    "state"=>$state,
                );
}

$js = '
	var jstrss = $("#jstree_demo_div").jstree({ "core" : {
	        "multiple" : true,
	        "animation" : 0,
	        "data" : '.json_encode($tags).'
	    } 
	});
';
if ($action=='select') {
	$js .= '
		jstrss.on("changed.jstree", function (e, data) {
		    window.opener.popupCate(data.node);
		    window.close();
		})
	';
}


Tak::regScript('bodyend-',$js);
?>
 <div id="jstree_demo_div"><div>