<?php if ($action == 'select'): ?>
<button onclick="window.close()">关闭</button>
<hr />
<?php
endif
?>
<?php
$this->regCssFile('jstree/default/style.min.css')->regScriptFile('plugins/jstree/jstree.min.js');
$data = Category::getCatsProduct();
$tags = array();
$temp = array();
if (isset($data[$id])) {
    $arrparentid = sprintf('$,%s,', $data[$id]['arrparentid']);
} else {
    $arrparentid = false;
}

foreach ($data as $key => $value) {
    $temp[] = array(
        'id' => $key,
        'name' => $value['catename']
    );
    $state = array();
    // 不存在ＩＤ时候默认展开一级分类
    if (!$id) {
        // if ($value['child'] == 0) {
        // $state['opened'] = true;
        // }
        
        
    } else {
        if (strpos($arrparentid, sprintf(',%s,', $key)) > 0) {
            $state['opened'] = true;
        }
        if ($key == $id) {
            $state['selected'] = true;
        }
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
        if(window.opener == undefined) {
            window.opener = window.dialogArguments;
        }   
        window.opener.popupCate(data.node);
            window.close();
        })
    ';
}

Tak::regScript('bodyend-', $js);
?>
<!--
<?php json_encode($temp); ?>
-->
 <div id="jstree_category"><div>
