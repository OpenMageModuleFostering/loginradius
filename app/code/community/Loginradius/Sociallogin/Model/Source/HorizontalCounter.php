<?php
 class Loginradius_Sociallogin_Model_Source_HorizontalCounter
 {
    public function toOptionArray()
    {
		$result = array();
	    $result[] = array('value' => 'vertical', 'class' => 'loginradius_radio', 'label'=>'<img style="width: 276px" src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Counter'.DS.'vertical.png',array('_area'=>'frontend')).'" /><br />');
        $result[] = array('value' => 'horizontal', 'class' => 'loginradius_radio', 'label'=>'<img style="width: 279px" src="'.Mage::getDesign()->getSkinUrl('Loginradius'.DS.'Sociallogin'.DS.'images'.DS.'Counter'.DS.'horizontal.png',array('_area'=>'frontend')).'" /><br />');
		return $result;
  	} 	
 }