<?php
class Loginradius_Sociallogin_Block_Info extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $html = $this->_getHeaderHtml($element);
        $html.= $this->_getFieldHtml($element);
        $html .= $this->_getFooterHtml($element);
        return $html;
    }
    protected function _getFieldHtml($fieldset) {
        $content = '<p>Social Login for Magento ' . Mage::getConfig()->getModuleConfig("Loginradius_Sociallogin")->version . '</p>';
        $content.= '<p>This extension is developed by <a href="http://loginradius.com/" target="_blank">LoginRadius</a>. Please refer to our <a href="http://support.loginradius.com/customer/portal/articles/682910-how-do-i-implement-social-login-on-my-magento-website-" target="_blank">Documentation</a> on how to install and configure this extension.</p>';
        $content.= '<p>Copyright &copy ' . date("Y") . ' <a href="http://loginradius.com/" target="_blank">LoginRadius, Inc.</a></p>';
        return $content;
    }
}