<?php
 class Loginradius_Sociallogin_Model_Source_Uihover
 {
    public function toOptionArray(){
		$result = array();
        $result[] = array('value' => 'same', 'label'=>__('Redirect to same page where the user logged in').'<br/>');
		$result[] = array('value' => 'account', 'label'=>__('Redirect to account page').'<br/>');
	    $result[] = array('value' => 'index', 'label'=>__('Redirect to home page').'<br/>');
	    $result[] = array('value' => 'custom', 'label'=>__('Redirect to following url') );
		return $result;
  	} 	
 }