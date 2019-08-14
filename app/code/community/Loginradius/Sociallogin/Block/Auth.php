<?php

class Loginradius_Sociallogin_Block_Auth extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {

    function loginradius_buttons() {
	  $block_anyplace = new Loginradius_Sociallogin_Block_Sociallogin();
	  $ApiKey = trim($block_anyplace->getApikey());
      $ApiSecrete = trim($block_anyplace->getApiSecret());
	  $UserAuth = $block_anyplace->getApiResult($ApiKey, $ApiSecrete);
	  $titleText = $this->getLabelText();
	  $errormsg = '<p style ="color:red;">To activate your plugin, please log in to LoginRadius and get API Key & Secret. Web: <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b></p>';
	  if ($block_anyplace->user_is_already_login()) {
	    $userName = Mage::getSingleton('customer/session')->getCustomer()->getName();
	    return '<span>Welcome!'.' '.$userName .'</span>';
      }
	  else {
	    if ($UserAuth == false) {
	       return $errormsg;
         }
        else {
	      $IsHttps = (!empty($UserAuth->IsHttps)) ? $UserAuth->IsHttps : '';
	      $iframeHeight = (!empty($UserAuth->height)) ? $UserAuth->height : 50;
	      $iframeWidth = (!empty($UserAuth->width)) ? $UserAuth->width : 138;
          $http = ($IsHttps == 1) ? "https://" : "http://";
	      $loc = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin/";
		   if (empty($titleText)) {
             $titleText = 'Please login with';
		   }
		  $label = '<span ><b>' . $titleText . '</b></span>';
          $iframe = '<iframe src="'.$http.'hub.loginradius.com/Control/PluginSlider2.aspx?apikey='.$ApiKey.'&callback='.$loc.'" width="'.$iframeWidth.'" height="'.$iframeHeight.'" frameborder="0" scrolling="no" allowtransparency="true"></iframe>';
		  return $label.$iframe;
       }
	 }
                
   }

    protected function _toHtml() {
        $content = '';
        if (Mage::getSingleton('customer/session')->isLoggedIn() == false)
            $content = $this->loginradius_buttons();
        return $content;
    }

    protected function _prepareLayout() {
        /*if ($this->getLayout()->getBlock('loginradius_sociallogin_scripts') == false) {
           $block = $this->getLayout()
                ->createBlock('core/template', 'loginradius_sociallogin_scripts')
                ->setTemplate('loginradius/sociallogin/auth.phtml');
            $this->getLayout()->getBlock('before_body_end')->insert($block);
        }
*/
        parent::_prepareLayout();
    }

}
