<?php

class Loginradius_Sociallogin_Model_System_Config_Tools
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

	/**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
        //$helper = Mage::helper('powershare');
		
		$html = $this->_getHeaderHtml($element);

		$html .= $this->_getButtonHtml();

        $html .= $this->_getFooterHtml($element);

        return $html;
	}
    
	private function _getButtonHtml()
	{
		die('test');
		$button = "<span>Button</span>";
	}

}
