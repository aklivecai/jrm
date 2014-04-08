<?php if ($action == 'select') :?>
<button onclick="window.close()">关闭</button>
<hr />
<?php endif ?>
<?php
$this->regCssFile('jstree/default/style.min.css')->regScriptFile('plugins/jstree/jstree.min.js');
$data = Category::getCatsProduct();
$tags = array();

$temp = array();
foreach ($data as $key => $value) {
    $temp[] = array(
        'id' => $key,
        'name' => $value['catename']
    );
    $state = array();
    $state['opened'] = true;
    if ($key == $id) {
        $state['selected'] = true;
    }
    $tags[] = array(
        'id' => $key,
        'parent' => ($value['parentid'] ? $value['parentid'] : '#') ,
        'text' => $value['catename'],
        "state" => $state,
    );
}

$js = '
	var jstrss = $("#jstree_category").jstree({ "core" : {
	        "multiple" : true,
	        "animation" : 0,
	        "data" : ' . json_encode($tags) . '
	    } 
	});
';
if ($action == 'select') {
    $js.= '
		jstrss.on("changed.jstree", function (e, data) {
		    window.opener.popupCate(data.node);
		    window.close();
		})
	';
}

Tak::regScript('bodyend-', $js);
?>
<!--
<?php  json_encode($temp); ?>
-->
 <div id="jstree_category"><div>
