<?php
 class Loginradius_Sociallogin_Model_Source_SharingProviders
 {
    public function toOptionArray()
    {
		$result = array();
        $result[] = array('value' => 'facebook', 'label'=>'Facebook');
	    $result[] = array('value' => 'googleplus', 'label'=>'Google Plus');
        $result[] = array('value' => 'twitter', 'label'=>'Twitter');
	    $result[] = array('value' => 'google', 'label'=>'Google');
        $result[] = array('value' => 'yahoo', 'label'=>'Yahoo');
	    $result[] = array('value' => 'reddit', 'label'=>'Reddit');
        $result[] = array('value' => 'email', 'label'=>'Email');
	    $result[] = array('value' => 'print', 'label'=>'Print');
        $result[] = array('value' => 'tumblr', 'label'=>'Tumblr');
	    $result[] = array('value' => 'linkedin', 'label'=>'LinkedIn');
        $result[] = array('value' => 'live', 'label'=>'Live');
	    $result[] = array('value' => 'vkontakte', 'label'=>'Vkontakte');
        $result[] = array('value' => 'digg', 'label'=>'Digg');
	    $result[] = array('value' => 'myspace', 'label'=>'Myspace');
        $result[] = array('value' => 'delicious', 'label'=>'Delicious');
	    $result[] = array('value' => 'hyves', 'label'=>'Hyves');
        $result[] = array('value' => 'dotnetkicks', 'label'=>'DotNetKicks');
	 	return $result;  
  	} 	
 }