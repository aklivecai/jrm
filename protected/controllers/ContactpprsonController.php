<?php

class ContactpPrsonController extends Controller
{
	public function init()  
	{     
    		parent::init();
    		$this->modelName = 'ContactpPrson';
	}
	protected function getSelectOption($q,$not=false){
		$result = parent::getSelectOption($q);
		$clienteleid = Tak::getQuery('clienteleid',false);	
		// Tak::KD($clienteleid);
		if ($clienteleid&&(int)$clienteleid>=0) {
			$result['data']['criteria']->compare('clienteleid',$clienteleid);
			// addCondition('clienteleid='.$clienteleid);
		}	
		 // Tak::KD($result,1);
		return $result;
	}
}
