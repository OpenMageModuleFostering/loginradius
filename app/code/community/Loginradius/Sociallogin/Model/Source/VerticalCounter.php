<?php
 class Loginradius_Sociallogin_Model_Source_VerticalCounter
 {
    public function toOptionArray()
    {
		$result = array();
        $result[] = array('value' => 'horizontal', 'class' => 'loginradius_radio', 'label'=>'<img style="margin-right:5px" src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Counter'.DS.'verticalhorizontal.png',array('_area'=>'frontend')).'" /><br />');
	    $result[] = array('value' => 'vertical', 'class' => 'loginradius_radio', 'label'=>'<img src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Counter'.DS.'verticalvertical.png',array('_area'=>'frontend')).'" /><br />');
	 	return $result;  
  	} 	
 }