<?php
class Loginradius_Sociallogin_Block_Auth extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {
	private $block_anyplace;
	public function __construct(){
		$this->block_anyplace = new Loginradius_Sociallogin_Block_Sociallogin();
	}
    function loginradius_buttons() {
	  $ApiKey = trim($this->block_anyplace->getApikey());
      $ApiSecrete = trim($this->block_anyplace->getApiSecret());
	  $UserAuth = $this->block_anyplace->getApiResult($ApiKey, $ApiSecrete);
	  $titleText = $this->getLabelText();
	  $errormsg = '<p style ="color:red;">To activate your plugin, please log in to LoginRadius and get API Key & Secret. Web: <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b></p>';
	  if ($this->block_anyplace->user_is_already_login()) {
	    $userName = Mage::getSingleton('customer/session')->getCustomer()->getName();
	    return '<span>Welcome!'.' '.$userName .'</span>';
      }else{
	    if( $ApiKey == "" && $ApiSecrete == "" ){
	       return $errormsg;
		}elseif( $UserAuth == false ){
			return '<p style ="color:red;">Your LoginRadius API key and secret is not valid, please correct it or contact LoginRadius support at <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b></p>';
		 }else {
	      $loc = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin/";
		   if (empty($titleText)) {
             $titleText = 'Social Login';
		   }
		  $label = '<span ><b>' . $titleText . '</b></span>';
		  $iframe = '<div class="interfacecontainerdiv" style="margin-left:10px"></div>';
		  return $label.$iframe;
       }
	 }
   }
    protected function _toHtml() {
        $content = '';
        if (Mage::getSingleton('customer/session')->isLoggedIn() == false && $this->block_anyplace->loginEnable() == "1" ){
            $content = $this->loginradius_buttons();
		}
        return $content;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
    }
}