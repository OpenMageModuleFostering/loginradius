<?php
 class Loginradius_Sociallogin_Model_Source_CounterTheme
 {
    public function toOptionArray()
    {
		$result = array();
        $result[] = array('value' => 'horizontal', 'label'=>'Horizontal');
	    $result[] = array('value' => 'vertical', 'label'=>'Vertical');
	 	return $result;  
  	} 	
 }