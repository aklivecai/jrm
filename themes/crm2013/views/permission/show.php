<?php
/* @var $this ClienteleController */
/* @var $model Clientele */

$this->breadcrumbs=array(
	Tk::g($this->modelName)=>array('admin'),
	$model->title,
);
	 $childDataProvider = $model->getChild();
	 $data = $childDataProvider->getData();

 ?>
   <div class="row-fluid">
    <?php foreach ($data as $key => $value) :?>
                            <div class="item clearfix">
                                <div class="info">
                                    <?php
                                        echo CHtml::link($value['description'],
                                            array('show','id'=>$id,'child'=>urlencode($crypt->encrypt($value['name'])))
                                                ,array('class'=>'name data-ajax','title'=>Tk::g(array($this->types[$value['type']],' - ',$value['description'])))
                                            );
                                    ?>                                                            
                                    <span>
                                    <?php echo $this->types[$value['type']];?>
                                    </span>                                   
                                </div>                                
                            </div>                                                                      
      <?php endforeach ?>    
</div>