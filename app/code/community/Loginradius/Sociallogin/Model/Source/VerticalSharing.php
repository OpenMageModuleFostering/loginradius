<?php
 class Loginradius_Sociallogin_Model_Source_VerticalSharing
 {
    public function toOptionArray()
    {
		$result = array();
        $result[] = array('value' => '32', 'label'=>'<img style="margin-right:5px" src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Sharing'.DS.'32VerticlewithBox.png',array('_area'=>'frontend')).'" /><br />');
	    $result[] = array('value' => '16', 'label'=>'<img src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Sharing'.DS.'16VerticlewithBox.png',array('_area'=>'frontend')).'" /><br />');
	 	return $result;  
  	} 	
 }