<?php
$this->pageTitle=Yii::app()->name . ' - 帮助中心';
$this->breadcrumbs=array(
  '帮助中心 ',
);

$model = new Movings;
?>
<div class="row-fluid">
        <div class="head clearfix">
            <div class="isw-chats"></div>
            <h1>销售提货出库</h1>

<ul class="buttons" id="yw0"><li><a class="save" href="javascript:;" id="yt0"><i class="isw-edit"></i> 保存</a></li><li><a href="javascript:;"><i class="isw-refresh"></i> 刷新</a></li><li><a href="javascript:;"><i class="isw-left"></i> 返回</a></li></ul>             
        </div>
        <div class="block-fluid clearfix">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'wizard_validate',
        'enableAjaxValidation'=>true,
        'enableClientValidation'=>true,
    )); ?>

                <fieldset title="第一步">
                    <legend>产品明细</legend>        
                    <div class="row-form clearfix">
                        <div class="span3">选择仓库</div>
                        <div class="span9">
                            <select>
                            <option>广州仓库1</option>
                            <option>广州仓库2</option>
                            <option>深圳仓库1</option>
                            <option>深圳仓库2</option>                                
                            </select>
                            <span class="bottom">出库的仓库</span>
                        </div>
                    </div>
                    <div class="row-form clearfix">
                        <div class="span3">产品信息</div>
                    </div>
                        <div class="row-form clearfix">
      <table cellpadding="0" cellspacing="0" width="100%" class="table" >
          <thead>
            <tr>
              <th>产品</th>
              <th width="80">规格</th>
              <th width="80">材料</th>
              <th width="80">颜色</th>
              <th width="80">单价</th>
              <th width="80">数量</th>              
              <th width="80">备注</th>
              <th width="30">移除</th>
            </tr>
          </thead>
          <tbody class="not-mpr" id="product-movings">
          <?php echo $strProducts;?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3">
                    <div>
                            <input type="text" class="sele1ct-product" placeholder="搜索产品" />
                    </div>
              </td>
              <td colspan="5"></td>
            </tr>
          </tfoot>
        </table>
                            <span class="bottom">显示对应仓库的产品(库存不大于0的标记<span class="red">红色.</span>不能选择)</span>
                        </div>
                </fieldset>

                <fieldset title="第二步">
                    <legend>单据信息</legend>
      <div class="row-form clearfix" style="border-top-width: 0px;"> <span class="span3">
        <?php echo $form->labelEx($model,'time'); ?></span> <span class="span9">
        <?php echo $form->dateField($model,'time',array('required'=>'required','size'=>10,'maxlength'=>10,'value'=>($model->time>0?Tak::timetodate($model->time):''))); ?>
        </span> </div>
        <div class="row-form clearfix"> <span class="span3"><?php echo $form->labelEx($model,'numbers'); ?></span> <span class="span9"><?php echo $form->textField($model,'numbers',array('size'=>60,'maxlength'=>100)); ?></span> </div>
        <div class="row-form clearfix"> <span class="span3"><?php echo $form->labelEx($model,'enterprise'); ?></span> <span class="span9"><?php echo $form->textField($model,'enterprise',array('required'=>'required','size'=>60,'maxlength'=>100)); ?></span> </div>
        <div class="row-form clearfix"> <span class="span3"><?php echo $form->labelEx($model,'us_launch'); ?></span> <span class="span9"><?php echo $form->textField($model,'us_launch',array('size'=>60,'maxlength'=>100)); ?></span> </div>
        <div class="row-form clearfix"> <span class="span3"><?php echo $form->labelEx($model,'note'); ?></span> <span class="span9"><?php echo $form->textArea($model,'note',array('size'=>60,'maxlength'=>255)); ?></span> </div>
                </fieldset>

                <input type="submit" class="btn finish" value="提交" />
<?php

  $this->endWidget(); 
  $this->regScriptFile('k-load-movings.js?t=1', CClientScript::POS_END);
?>

        </div>
    </div>

</div>
