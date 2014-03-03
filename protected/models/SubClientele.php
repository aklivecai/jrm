<?php
class SubClientele extends Clientele
{

	public $isLog = false;/*是否记录日志*/
	//默认继承的搜索条件
	public function defaultScope()
	{
		$arr = parent::defaultScope();
		$sql = Subordinate::getSubManageSql();
		$condition = array(
			'status=1',
			$sql
		);
		if (false&&isset($arr['condition'])) {
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
