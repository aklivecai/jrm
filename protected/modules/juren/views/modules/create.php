<?php
/* @var $this TestMemeberController */
/* @var $model TestMemeber */

$this->breadcrumbs = array(
    Tk::g('Modules') => array(
        'admin'
    ) ,
    Tk::g('Create') ,
);
?>
<?php $this->renderPartial('_form', array(
    'model' => $model
)); ?>