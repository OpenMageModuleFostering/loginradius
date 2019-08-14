<?php
 class Loginradius_Sociallogin_Model_Source_CounterProviders
 {
    public function toOptionArray()
    {
		$result = array();
        $result[] = array('value' => 'Facebook Like', 'label'=>'Facebook Like');
	    $result[] = array('value' => 'Facebook Recommend', 'label'=>'Facebook Recommend');
        $result[] = array('value' => 'Facebook Send', 'label'=>'Facebook Send');
	    $result[] = array('value' => 'Google+ +1', 'label'=>'Google+ +1');
        $result[] = array('value' => 'Google+ Share', 'label'=>'Google+ Share');
	    $result[] = array('value' => 'LinkedIn Share', 'label'=>'LinkedIn Share');
        $result[] = array('value' => 'Twitter Tweet', 'label'=>'Twitter Tweet');
	    $result[] = array('value' => 'StumbleUpon Badge', 'label'=>'StumbleUpon Badge');
	    $result[] = array('value' => 'Reddit', 'label'=>'Reddit');
	 	return $result;  
  	} 	
 }