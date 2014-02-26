<?php

class Test9Memeber extends TestMemeber
{

    public static function model($className=__CLASS__)
    {
    	return parent::model($className);
    }
    //默认继承的搜索条件
    public function defaultScope()
    {
    	$arr = parent::defaultScope();
    	$condition = array();
    	if(isset($arr['condition'])){
    		$condition[] = $arr['condition'];
    	}
    	if (!Tak::checkSuperuser()) {
    		$condition[] = "manageid=".Tak::getManageid();
    	}
            $get = Yii::app()->request->getQuery('t',false);
            
            if ($get) {
               $condition[] = "active_time>0"; 
               if ($get==1) {
                $active_time = Tak::now();
                $t = mktime(23,59,59,date("m",$active_time),date("d",$active_time)-15,date("Y",$active_time));
                   $condition[] = "active_time<$t"; 
               }
            }        
    	// $condition[] = 'display>0';
    	$arr['condition'] = join(" AND ",$condition);
    	return $arr;
    }
}
