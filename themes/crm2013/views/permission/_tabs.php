<?php
if (!isset($action)) {
    $action = 'Create';
}
$items = array(
    'action' => array('label'=>Tk::g(array($action,'Permission')),'url'=>'#')
);
if ($this->tabs) {
    foreach ($this->tabs as $key => $value) {
        $items[] = array('label'=>$value,'url'=>array('permission/view','id'=>$key));
    }
    if (isset($id)&&$id>0) {
        $items['action']=array('label'=>'部门操作', 'items'=>array(
                    array('label'=>'添加', 'url'=>array('permission/create')
                    , 'linkOptions'=>array('class'=>'data-ajax','title'=>Tk::g(array('Create',$this->modelName)))
                    ),
                )
        );        
        $items['action']['items'][] = array('label'=>'修改', 'url'=>array('permission/update','id'=>$id),'linkOptions'=>array('class'=>'data-ajax','title'=>Tk::g(array('Update',$this->modelName))));
        $items['action']['items'][] = array('label'=>'删除','linkOptions'=>array('class'=>'red delete','data-title'=>$model->title) , 'url'=>array('permission/delete','id'=>$id));
    }else{
        $items['action']['active'] = true;    
    }    
}else{
    $items['action']['active'] = true;
}

$this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'items'=>$items,
)); 
