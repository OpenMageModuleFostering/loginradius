<?php
class Loginradius_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template
{
	protected function _construct(){
        parent::_construct();
		if( $this->shareEnable() == "1" || $this->counterEnable() == "1" ){
        	$this->setTemplate('sociallogin/socialshare.phtml');
    	}
	}
	public function _prepareLayout(){
		return parent::_prepareLayout();
    }
    public function getSociallogin(){ 
        if (!$this->hasData('sociallogin')) {
            $this->setData('sociallogin', Mage::registry('sociallogin'));
        }
        return $this->getData('sociallogin');
    }
	public function user_is_already_login() {
	   if( Mage::getSingleton('customer/session')->isLoggedIn() ){
		 return true;
	   }
	   return false;
    }
	public function loginEnable(){
       return Mage::getStoreConfig('sociallogin_options/messages/loginEnable');
     }
	public function getApikey(){
       return Mage::getStoreConfig('sociallogin_options/messages/appid');
     }
	public function getAvatar( $id ){
		$socialLoginConn = Mage::getSingleton('core/resource')
							->getConnection('core_read');
		$SocialLoginTbl = Mage::getSingleton('core/resource')->getTableName("sociallogin");
		$select = $socialLoginConn->query("select avatar from $SocialLoginTbl where entity_id = '$id' limit 1");
		if( $rowArray = $select->fetch() ) {  
			if( ($avatar = trim($rowArray['avatar'])) != "" ){
				return $avatar;
			}
		}
		return false;
	 }
	public function getShowDefault()
	 {
       return Mage::getStoreConfig('sociallogin_options/messages/showdefault');
     }
	public function getAboveLogin()
	 {
       return Mage::getStoreConfig('sociallogin_options/messages/aboveLogin');
     }
	public function getBelowLogin()
	 {
       return Mage::getStoreConfig('sociallogin_options/messages/belowLogin');
     }
	public function getAboveRegister()
	 {
       return Mage::getStoreConfig('sociallogin_options/messages/aboveRegister');
     }
	public function getBelowRegister()
	 {
       return Mage::getStoreConfig('sociallogin_options/messages/belowRegister');
     }
	public function getApiSecret()
      {	 
    	return Mage::getStoreConfig('sociallogin_options/messages/appkey');
      }
	public function getLoginRadiusTitle()
      {	 
    	return Mage::getStoreConfig('sociallogin_options/messages/loginradius_title');
      }
      public function getRedirectOption(){
    
    	return Mage::getStoreConfig('sociallogin_options/messages/redirect');
      }
      public function getApiOption(){
    
    	return Mage::getStoreConfig('sociallogin_options/messages/api');
      }
	  
	  public function getEmailRequired(){
    
    	return Mage::getStoreConfig('sociallogin_options/email_settings/emailrequired');
      }
	  public function verificationText(){
    
    	return Mage::getStoreConfig('sociallogin_options/email_settings/verificationText');
      }
	  public function getPopupText(){
    
    	return Mage::getStoreConfig('sociallogin_options/email_settings/popupText');
      }
	  public function getPopupError(){
    
    	return Mage::getStoreConfig('sociallogin_options/email_settings/popupError');
      }
	  public function getLinking(){
    	return Mage::getStoreConfig('sociallogin_options/messages/socialLinking');
	  }
	  public function notifyUser(){
    	return Mage::getStoreConfig('sociallogin_options/email_settings/notifyUser');
	  }
	  public function notifyUserText(){
    	return Mage::getStoreConfig('sociallogin_options/email_settings/notifyUserText');
	  }
	  public function notifyAdmin(){
    	return Mage::getStoreConfig('sociallogin_options/email_settings/notifyAdmin');
	  }
	  public function notifyAdminText(){
    	return Mage::getStoreConfig('sociallogin_options/email_settings/notifyAdminText');
	  }
	  public function shareEnable(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/shareEnable');
	  }
	  public function sharingCode(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/sharingCode');
	  }
	  public function counterEnable(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterEnable');
	  }
	  public function counterCode(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterCode');
	  }
	  public function getCallBack(){
    	return Mage::getStoreConfig('sociallogin_options/messages/call');
      }
	  public function getProfileResult($ApiSecrete) 
	  { 
	    if(isset($_REQUEST['token'])) {
		  $ValidateUrl = "https://hub.loginradius.com/userprofile.ashx?token=".$_REQUEST['token']."&apisecrete=".trim($ApiSecrete);
		  return $this->getApiCall($ValidateUrl);
		}
	  }
	  public function getApiResult($ApiKey, $ApiSecrete) 
	  { 
	    if ( !empty($ApiKey) && !empty($ApiSecrete) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $ApiKey) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $ApiSecrete) ) {
		  return true;
		}
		else {
		   return false;
		}
      }
      public function getApiCall($url)
       {
			if ( $this->getApiOption() == 'curl' ){
				$curl_handle = curl_init();
				curl_setopt($curl_handle, CURLOPT_URL, $url);
				curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 3);
				curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
				if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' or !ini_get('safe_mode'))) {
				  curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
				  curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
				  $JsonResponse = curl_exec($curl_handle);
				}else {
				  curl_setopt($curl_handle, CURLOPT_HEADER, 1);
				  $url = curl_getinfo($curl_handle, CURLINFO_EFFECTIVE_URL);
				  curl_close($curl_handle);
				  $ch = curl_init();
				  $url = str_replace('?','/?',$url);
				  curl_setopt($ch, CURLOPT_URL, $url);
				  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				  $JsonResponse = curl_exec($ch);
				  curl_close($ch);
			   }
		 }
		 elseif($this->getApiOption() == 'fopen') {
		    $JsonResponse = file_get_contents($url);
		 }else {
	       $method = 'GET';
		   try{
			   $client = new Varien_Http_Client($url);
			   $response = $client->request($method);
			   $JsonResponse = $response->getBody();
		   }catch(Exception $e){
		   }
		 }  
			if ($JsonResponse) {
               return json_decode($JsonResponse);
            }
			else {
               return "something went wrong, Can not get api response.";
            }
            
      }
}