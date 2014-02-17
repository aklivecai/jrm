<?php

class MovesForm extends CFormModel
{
	                 
	public $fMid;
	public $tMid;
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fMid,tMid', 'required'),
			array('fMid,tMid', 'numerical', 'integerOnly'=>true),
			array('fMid,tMid', 'checkMid'),
			array('tMid', 'checkRa'),
		);
	}
	public function checkRa($attribute,$params){		
		if ($this->tMid!=''&&$this->tMid==$this->fMid) {
			$this->addError($attribute,'一样的用户不允许转移');
		}	
	}
	public function checkMid($attribute,$params){
		$v = $this->$attribute;
		if ($v ) {
			$result = Manage::model()->findByPk($v);
			if ($result===null) {
				$this->addError($attribute,'不存在此用户');
			}	
		}
	}
	public function attributeLabels()
	{
		return array(
				'fMid' => '来源用户',
				'tMid' => '转移用户',
		);
	}

	public function moveClienteles($clienteleid=false){
		$arr = array(
				':fmid'=>$this->fMid,
				':tmid'=>$this->tMid,

				':c'=>'{{clientele}}',
				':cp'=>'{{contactp_prson}}',
				':cc'=>'{{contact}}',
		);
		$sqls = array();
		$result = array();
		$sqlXXXX = '
		UPDATE :c c, :cp cp ,:cc cc
			SET c.manageid = :tmid
				,cp.manageid = :tmid
				,cc.manageid = :tmid
			WHERE c.manageid = :fmid AND c.itemid=cp.clienteleid AND
			 c.itemid = cc.clienteleid
		 ';
		 $connection = Yii::app()->db;
		 $transaction = $connection->beginTransaction();
		 foreach (array('c','cp','cc')  as $key=>$value) {
		 	 $_sql  = "UPDATE :$value SET manageid = :tmid WHERE manageid = :fmid ";
		 	if ($clienteleid) {
		 		$_sql .= ' AND ';
		 		$_sql .= $key==0?'itemid':'clienteleid';
		 		$_sql.="=$clienteleid";
		 	}
		 	$_sql.=';';
		 	$sqls[$value] = $_sql;
		 }
		 // Tak::KD($sqls);
		try
		{		 
		    foreach ($sqls as $key=>$value) {
		    	$sql = strtr($value,$arr);
		    	// Tak::KD($sql);
		    	$temp = $connection->createCommand($sql)->execute();
		    	$result[$key] = $temp;
		    }			
		    // Tak::KD($result,1);

					$str = '成功转移 <br />客户 <span class="red">:c</span> ,<br /> 联系人<span class="red">:cp</span>, <br />联系记录<span class="red">:cc</span>';
					$str = strtr($str,$result);	   
		    	AdminLog::log(Tk::g('Clientele','Moves').'  '.$str);
		}
		catch(Exception $e) // 如果有一条查询失败，则会抛出异常
		{
			Tak::KD($e,1);
		    $transaction->rollBack();
		    return false;
		}				
		return $result;		
	}	
}
