<?php 
class LRecord extends CActiveRecord
{
	public $mName = null;
	public $sName = null;

	public static $table = null;
	public function init()
	{
        $this->mName === null &&$this->mName = get_class($this);
        $this->sName = Tk::g($this->mName);		
	}	
	public function tableName()
	{
		$m = get_class($this);
		return $m::$table;
	}

    protected function getItemid() {
        if ($this->hasAttribute('itemid')) {
            $result = $this->itemid;
        } else {
            $primary = $this->primaryKey();
            $result = $this->{$primary};
        }
        return $result;
    }	
	public function primaryKey(){
		return 'itemid';
	}
	public function rules()
	{
		return array(
			// array('itemid','autoID','on'=>'create'),
			// array('itemid','required'),
		);
	}

	public function autoID($attribute,$params)
	{
		$val = null;
		if ($attribute=='fromid') {
			$val = Tak::getFormid();
		}elseif($attribute=='manageid'){
			$val = Tak::getManageid();
		}else{

		}
		if ($val!==null) {
			$this->$attribute = $val;
		}
	}
	public function addFastUuid($attribute,$params)
	{
		$this->$attribute = Tak::fastUuid();
	}
	public function addNow($attribute,$params)
	{
		$this->$attribute = Tak::now();
	}
	public function checkRepetition($attribute, $params)
	{
		$sql = array('LOWER(:col)=:val');
		$arr = array(':col'=>$attribute);
		$itemid = $this->getItemid();
		if ($itemid>0) {
			$sql[] = ':key<>:itemid';
			$arr[':key'] = $this->primaryKey();
			$arr[':itemid'] = $itemid;
		}
		$sql = join(' AND ', $sql);
		$sql = strtr($sql,$arr);
		$m = $this->find($sql,array(
			':val' => strtolower($this->$attribute)
		));
		if ($m!==null) {
			$err = $this->getAttributeLabel($attribute) . '已经存在!';
			$this->addError($attribute,$err);
		}
	}

	public function defaultScope() {
		$arr = array();
		$condition = array();
		$condition[] = 'fromid=' . Tak::getFormid();
		$arr['condition'] = join(" AND ", $condition);
		return $arr;
	}

    protected function getOjb($opt = 'next', $isid = false,$orderCol=false) {
        $m = $this->mName;
        $result = null;
        $itemid = $this->getItemid();
        $arr = array(
            'pre' => array(
                'opt' => '<',
                'order' => 'DESC'
            ) ,
            'next' => array(
                'opt' => '>',
                'order' => 'ASC'
            ) ,
        );
        if ($itemid > 0 && isset($arr[$opt])) {
            $t = $arr[$opt];
        } else {
            return null;
        }
        $col = $isid ? ':itemid' : '*';
        $orderCol = $orderCol?$orderCol:':itemid';
        $sqlWhere = array(
            $this->defaultScope()['condition']
        );
        $sqlWhere[] = ':itemid :opt :current_id';
        $sqlWhere = array_filter($sqlWhere);
        $sqlWhere = join(" AND ", $sqlWhere);
        
        $sql = "SELECT $col FROM :tableName WHERE $sqlWhere ORDER BY $orderCol :order ";
        // LIMIT 1
        // 
        
        $sql = strtr($sql, array(
            ':tableName' => $this->tableName(),
            ':sqlWhere' => $sqlWhere,
            ':itemid' => $this->primaryKey() ,
            ':current_id' => $itemid,
            ':opt' => $t['opt'],
            ':order' => $t['order']
        ));
        $dataReader = self::$db->createCommand($sql)->queryRow();
        if (count($dataReader) > 0) {
            $result = $dataReader;
        }
        return $result;
    }
   
    public function getNext($isid) {
        $result = $this->getOjb('next', $isid);
        if ($result && $isid) {
            $result = current($result[0]);
        }
        return $result;
    }
    
    public function getPrevious($isid) {
        $result = $this->getOjb('pre');
        if ($result && $isid) {
            $result = current($result[0]);
        }
        return $result;
    }

}