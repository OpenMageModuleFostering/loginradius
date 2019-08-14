<?php
 class Loginradius_Sociallogin_Model_Source_Uihover
 {
    public function toOptionArray()
    {
		$result = array();
        $result[] = array('value' => 'account', 'label'=>'&nbsp;&nbsp;&nbsp;Redirect to Account page<br/>');
	    $result[] = array('value' => 'index', 'label'=>'&nbsp;&nbsp;Redirect to Home page<br/>');
	    $result[] = array('value' => 'custom', 'label'=>'&nbsp;&nbsp;Redirect to following url' );
	 return $result;  
  	} 	
 }