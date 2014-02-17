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
    	// $condition[] = 'display>0';
    	$arr['condition'] = join(" AND ",$condition);
    	return $arr;
    }
}
