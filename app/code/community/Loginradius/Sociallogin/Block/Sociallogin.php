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
	  public function getProfileFieldsRequired(){
    	return Mage::getStoreConfig('sociallogin_options/email_settings/profileFieldsRequired');
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
	  public function shareProduct(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/shareProduct');
	  }
	  public function shareSuccess(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/shareSuccess');
	  }
	  public function sharingTitle(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/sharingTitle');
	  }
	  public function sharingTheme(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/sharingTheme');
	  }
	  public function horizontalSharing(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/horizontalSharing');
	  }
	  public function verticalSharing(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/verticalSharing');
	  }
	  public function sharingVerticalAlignment(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/sharingVerticalAlignment');
	  }
	  public function sharingOffset(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/sharingOffset');
	  }
	  public function sharingProviders(){
    	return Mage::getStoreConfig('sociallogin_options/sharing/sharingProvidersHidden');
	  }
	  public function counterEnable(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterEnable');
	  }
	  public function counterProduct(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterProduct');
	  }
	  public function counterSuccess(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterSuccess');
	  }
	  public function counterTitle(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterTitle');
	  }
	  public function counterTheme(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterTheme');
	  }
	  public function horizontalCounter(){
    	return Mage::getStoreConfig('sociallogin_options/counter/horizontalCounter');
	  }
	  public function verticalCounter(){
    	return Mage::getStoreConfig('sociallogin_options/counter/verticalCounter');
	  }
	  public function counterVerticalAlignment(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterVerticalAlignment');
	  }
	  public function counterOffset(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterOffset');
	  }
	  public function counterProviders(){
    	return Mage::getStoreConfig('sociallogin_options/counter/counterProvidersHidden');
	  }
	  public function getCallBack(){
    	return Mage::getStoreConfig('sociallogin_options/messages/call');
      }
	  public function getProfileResult($ApiSecrete) 
	  { 
	    if(isset($_REQUEST['token'])) {
		  $ValidateUrl = "http://hub.loginradius.com/userprofile.ashx?token=".$_REQUEST['token']."&apisecrete=".trim($ApiSecrete);
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
	   		$JsonResponse = "";
			if ( $this->getApiOption() == 'curl' ){
				$curl_handle = curl_init();
				curl_setopt($curl_handle, CURLOPT_URL, $url);
				curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5); 
				curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
				if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' or !ini_get('safe_mode'))) {
				  curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
				  curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
				}else {
					curl_setopt($curl_handle, CURLOPT_HEADER, 1); 
					$url = curl_getinfo($curl_handle, CURLINFO_EFFECTIVE_URL);
					curl_close($curl_handle);
					$curl_handle = curl_init(); 
					$url = str_replace('?','/?',$url); 
					curl_setopt($curl_handle, CURLOPT_URL, $url); 
					curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
			    }
			    $JsonResponse = curl_exec($curl_handle);
				$httpCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
				if(in_array($httpCode, array(400, 401, 403, 404, 500, 503)) && $httpCode != 200){
					return "service connection timeout";
				}else{
					if(curl_errno($curl_handle) == 28){
						return "timeout";
					}
				}
			    curl_close($curl_handle);
		 }
		 elseif($this->getApiOption() == 'fopen') {
		    $JsonResponse = @file_get_contents($url);
			if(strpos(@$http_response_header[0], "400") !== false || strpos(@$http_response_header[0], "401") !== false || strpos(@$http_response_header[0], "403") !== false || strpos(@$http_response_header[0], "404") !== false || strpos(@$http_response_header[0], "500") !== false || strpos(@$http_response_header[0], "503") !== false){
				return "service connection timeout";
			}
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