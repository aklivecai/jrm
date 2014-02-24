<?php
class SubClientele extends Clientele
{

	//默认继承的搜索条件
	public function defaultScope()
	{
		$arr = parent::defaultScope();
		$sql = Subordinate::getSubManageSql();
		$condition = array(
			$sql
		);
		if (isset($arr['condition'])) {
			$condition[] = $arr['condition'];
		}

		$arr['condition'] = join(" AND ",$condition);
		return $arr;
	}	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
