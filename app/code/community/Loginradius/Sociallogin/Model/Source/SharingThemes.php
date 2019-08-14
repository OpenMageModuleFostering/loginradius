<?php
 class Loginradius_Sociallogin_Model_Source_SharingThemes
 {
    public function toOptionArray()
    {
		$result = array();
        $result[] = array('value' => '32', 'label'=>'<img src="Images/Sharing/horizonSharing32.png" />');
	    $result[] = array('value' => '16', 'label'=>'<img src="Images/Sharing/horizonSharing16.png" />');
	    $result[] = array('value' => 'single_large', 'label'=>'<img src="Images/Sharing/single-image-theme-large.png" />');
	    $result[] = array('value' => 'single_small', 'label'=>'<img src="Images/Sharing/single-image-theme-small.png" />');
	 	return $result;  
  	} 	
 }