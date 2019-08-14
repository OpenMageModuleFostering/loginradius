<?php
 class Loginradius_Sociallogin_Model_Source_HorizontalSharing
 {
    public function toOptionArray()
    {
		$result = array();
        $result[] = array('value' => '32', 'label'=>'<img width="274" src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Sharing'.DS.'horizonSharing32.png',array('_area'=>'frontend')).'" /><br />');
	    $result[] = array('value' => '16', 'label'=>'<img src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Sharing'.DS.'horizonSharing16.png',array('_area'=>'frontend')).'" /><br />');
	    $result[] = array('value' => 'single_large', 'label'=>'<img src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Sharing'.DS.'single-image-theme-large.png',array('_area'=>'frontend')).'" /><br />');
	    $result[] = array('value' => 'single_small', 'label'=>'<img src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Sharing'.DS.'single-image-theme-small.png',array('_area'=>'frontend')).'" />');
	 	return $result;  
  	} 	
 }