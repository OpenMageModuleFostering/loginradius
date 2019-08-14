<?php
Mage::app('default');
include 'Popup.php';
function getMazeTable($tbl){
	$tableName = Mage::getSingleton('core/resource')->getTableName($tbl);
	return($tableName);
}
//customer will be re-directed to this file. this file handle all token, email etc things.
class Loginradius_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action
{
	var $blockObj;
	private $loginRadiusPopMsg;
	private $loginRadiusPopErr;
	
	function redirect($url){
		?>
		<script>
		if(window.opener){
			window.opener.location.href = "<?php echo $url; ?>";
			window.close();
		}else{
			window.location.href = "<?php echo $url; ?>";
		}
		</script>
		<?php
		die;
	}
	
	protected function _getSession(){
		return Mage::getSingleton('sociallogin/session');
	}
	// if token is posted then this function will be called. It will login user if already in database. else if email is provided by api, it will insert data and login user. It will handle all after token.
	function tokenHandle() {
		$ApiSecrete = $this->blockObj->getApiSecret();
		$user_obj = $this->blockObj->getProfileResult($ApiSecrete);
		// validate the object
		if(is_object($user_obj) && isset($user_obj->ID)){
			$id = $user_obj->ID;
		}else{
			$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
		}
		if(empty($id)){
			//invalid user
			$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
			exit();
		}
		// social linking variable
		$socialLinking = false;
		// social linking
		if(isset($_GET['loginRadiusLinking']) && trim($_GET['loginRadiusLinking']) == 1){
			$socialLinking = true;
		}
		//valid user, checking if user in sociallogin table
		$socialLoginIdResult = $this->loginRadiusRead( "sociallogin", "get user", array($id), true );
		$socialLoginIds = $socialLoginIdResult->fetchAll();
		
		// variable to hold user id of the logged in user
		$sociallogin_id = '';
		foreach( $socialLoginIds as $socialLoginId ){
			// check if the user exists in the customer_entity table for this social id
			$select = $this->loginRadiusRead( "customer_entity", "get user2", array($socialLoginId['entity_id']), true );
			if($rowArray = $select->fetch()){
				if( $socialLoginId['verified'] == "0" ){
					if(!$socialLinking){
						$this -> setTmpSession("Please verify your email to login.", "", false);
						$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
					}else{
						// link account
						$this->loginRadiusSocialLinking(Mage::getSingleton("customer/session")->getCustomer()->getId(), $user_obj->ID, $user_obj->Provider, $user_obj->ThumbnailImageUrl);
						$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."customer/account/?LoginRadiusLinked=1");
						die;
					}
				}
				$sociallogin_id = $rowArray['entity_id'];
				break;
			}
		}
		
		if(!empty($sociallogin_id)){	//user is in database
			if(!$socialLinking){
				if($this->blockObj->updateProfileData() != '1'){
					$this->socialLoginUserLogin( $sociallogin_id, $id );
					return;
				}else{
					$socialloginProfileData = $this->socialLoginFilterData( '', $user_obj );
					$socialloginProfileData['lrId'] = $user_obj->ID;
					$this->socialLoginAddNewUser( $socialloginProfileData, $verify = false, true, $sociallogin_id );
					return;
				}
			}else{
				// account already exists
				$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."customer/account/?LoginRadiusLinked=0");
				die;
			}
		}
		// social linking
		if($socialLinking){
			$this->loginRadiusSocialLinking(Mage::getSingleton("customer/session")->getCustomer()->getId(), $user_obj->ID, $user_obj->Provider, $user_obj->ThumbnailImageUrl, true);
			$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."customer/account/?LoginRadiusLinked=1");
			die;
		}
		// initialize email
		$email = '';
		if( !empty($user_obj->Email[0]->Value) ){
			//if email is provided by provider then check if it's in table
			$email = $user_obj->Email['0']->Value;
			$select = $this->loginRadiusRead( "customer_entity", "email exists login", array($email), true );
			if( $rowArray = $select->fetch() ) {
				$sociallogin_id = $rowArray['entity_id'];
				if(!empty($sociallogin_id)) {
					//user is in customer table
					if( $this->blockObj->getLinking() == "1" ){    // Social Linking
						$this->loginRadiusSocialLinking($sociallogin_id, $user_obj->ID, $user_obj->Provider, $user_obj->ThumbnailImageUrl);
					}
					if($this->blockObj->updateProfileData() != '1'){
						$this->socialLoginUserLogin( $sociallogin_id, $user_obj->ID );
						return;
					}else{
						$socialloginProfileData = $this->socialLoginFilterData( '', $user_obj );
						$socialloginProfileData['lrId'] = $user_obj->ID;
						$this->socialLoginAddNewUser( $socialloginProfileData, $verify = false, true, $sociallogin_id );
						return;
					}
				}
			}
			$socialloginProfileData = $this->socialLoginFilterData( $email, $user_obj );
			$socialloginProfileData['lrId'] = $user_obj->ID;
			if($this->blockObj->getProfileFieldsRequired() == 1){
				$id = $user_obj->ID;
				$this->setInSession($id, $socialloginProfileData);
				$this -> setTmpSession("Please provide following details", "", true, $socialloginProfileData, false);
				// show a popup to fill required profile fields 
				$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
				return;
			}
			$this->socialLoginAddNewUser( $socialloginProfileData );
			return;
		}

		// empty email
		if( $this->blockObj->getEmailRequired() == 0 ) { 	// dummy email
			$email = $this->loginradius_get_randomEmail( $user_obj );
			$socialloginProfileData = $this->socialLoginFilterData( $email, $user_obj );
			$socialloginProfileData['lrId'] = $user_obj->ID;
			if($this->blockObj->getProfileFieldsRequired() == 1){
				$id = $user_obj->ID;
				//$socialloginProfileData = $this->socialLoginFilterData( $email, $user_obj );
				$this->setInSession($id, $socialloginProfileData);
				$this -> setTmpSession("Please provide following details", "", true, $socialloginProfileData, false);
				// show a popup to fill required profile fields
				$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
				return;
			}
			// create new user
			$this->socialLoginAddNewUser( $socialloginProfileData );
			return;
		}else {		// show popup
			$id = $user_obj->ID;
			$socialloginProfileData = $this->socialLoginFilterData( $email, $user_obj );
			$this->setInSession($id, $socialloginProfileData);
			if($this->blockObj->getProfileFieldsRequired() == 1){
				// show a popup to fill required profile fields 
				$this -> setTmpSession("Please provide following details", "", true, $socialloginProfileData, true);
				$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
			}else{
				$this -> setTmpSession($this->loginRadiusPopMsg, "", true, array(), true, true);
				$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
			}
			return;
		}
	}
	
    function loginradius_get_randomEmail( $user_obj ) {
      switch ( $user_obj->Provider ) {
        case 'twitter':
          $email = $user_obj->ID. '@' . $user_obj->Provider . '.com';
          break;
        case 'linkedin':
          $email = $user_obj->ID. '@' . $user_obj->Provider . '.com';
          break;
        default:
          $Email_id = substr( $user_obj->ID, 7 );
          $Email_id2 = str_replace("/", "_", $Email_id);
          $email = str_replace(".", "_", $Email_id2) . '@' . $user_obj->Provider . '.com';
          break;
      }
	  return $email;
    }
	// social linking
	function loginRadiusSocialLinking($entityId, $socialId, $provider, $thumbnail, $unique = false){
		// check if any account from this provider is already linked
		if($unique && $this->loginRadiusRead( "sociallogin", "provider exists in sociallogin", array($entityId, $provider))){
			$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."customer/account/?LoginRadiusLinked=2");
			die;
		}
		$socialLoginLinkData = array();
		$socialLoginLinkData['sociallogin_id'] = $socialId;
		$socialLoginLinkData['entity_id'] = $entityId;
		$socialLoginLinkData['provider'] = empty($provider) ? "" : $provider;
		$socialLoginLinkData['avatar'] = $this->socialLoginFilterAvatar( $socialId, $thumbnail, $provider );
		$socialLoginLinkData['avatar'] = ($socialLoginLinkData['avatar'] == "") ? NULL : $socialLoginLinkData['avatar'] ;
		$this->SocialLoginInsert( "sociallogin", $socialLoginLinkData );
	}
	function socialLoginFilterData( $email, $user_obj ) {
		$socialloginProfileData = array();
		$socialloginProfileData['Email'] = $email;
		$socialloginProfileData['Provider'] = empty($user_obj->Provider) ? "" : $user_obj->Provider;
		$socialloginProfileData['FirstName'] = empty($user_obj->FirstName) ? "" : $user_obj->FirstName;
		$socialloginProfileData['FullName'] = empty($user_obj->FullName) ? "" : $user_obj->FullName;
		$socialloginProfileData['NickName'] = empty($user_obj->NickName) ? "" : $user_obj->NickName;
		$socialloginProfileData['LastName'] = empty($user_obj->LastName) ? "" : $user_obj->LastName;
		if(isset($user_obj->Addresses) && is_array($user_obj->Addresses)){
			foreach($user_obj->Addresses as $address){
				if(isset($address->Address1) && !empty($address->Address1)){
					$socialloginProfileData['Address'] = $address->Address1;
					break;
				}
			}
		}elseif(isset($user_obj->Addresses) && is_string($user_obj->Addresses)){
			$socialloginProfileData['Address'] = isset($user_obj->Addresses) && $user_obj->Addresses != "" ? $user_obj->Addresses : "";
		}else{
			$socialloginProfileData['Address'] = "";
		}
		$socialloginProfileData['PhoneNumber'] = empty( $user_obj->PhoneNumbers['0']->PhoneNumber ) ? "" : $user_obj->PhoneNumbers['0']->PhoneNumber;
		$socialloginProfileData['State'] = empty($user_obj->State) ? "" : $user_obj->State;
		$socialloginProfileData['City'] = empty($user_obj->City) || $user_obj->City == "unknown" ? "" : $user_obj->City;
		$socialloginProfileData['Industry'] = empty($user_obj->Positions['0']->Comapny->Name) ? "" : $user_obj->Positions['0']->Comapny->Name;
		if(isset($user_obj->Country->Code) && is_string($user_obj->Country->Code)){
			$socialloginProfileData['Country'] = $user_obj->Country->Code;
		}else{
			$socialloginProfileData['Country'] = "";
		}
		$socialloginProfileData['thumbnail'] = $this->socialLoginFilterAvatar( $user_obj->ID, $user_obj->ThumbnailImageUrl, $socialloginProfileData['Provider'] );
		
		
		if(empty($socialloginProfileData['FirstName'])){
			if(!empty($socialloginProfileData['FullName'])){
				$socialloginProfileData['FirstName'] = $socialloginProfileData['FullName'];
			}
			elseif(!empty($socialloginProfileData['ProfileName'])){
				$socialloginProfileData['FirstName'] = $socialloginProfileData['ProfileName'];
			}
			elseif(!empty($socialloginProfileData['NickName'])){
				$socialloginProfileData['FirstName'] = $socialloginProfileData['NickName'];
			}elseif(!empty($email)){
				$user_name = explode('@', $email);
				$username = $user_name[0];
				$socialloginProfileData['FirstName'] = str_replace("_", " ", $user_name[0]);
			}else{
				$socialloginProfileData['FirstName'] = $user_obj->ID;
			}
		}
		
		$socialloginProfileData['Gender'] = (!empty($user_obj->Gender) ? $user_obj->Gender : '');
		if( strtolower(substr($socialloginProfileData['Gender'], 0, 1)) == 'm' ){
			$socialloginProfileData['Gender'] = '1';
		}elseif( strtolower(substr($socialloginProfileData['Gender'], 0, 1)) == 'f' ){
			$socialloginProfileData['Gender'] = '2';
		}else{
			$socialloginProfileData['Gender'] = '';
		}
		$socialloginProfileData['BirthDate'] = (!empty($user_obj->BirthDate) ? $user_obj->BirthDate : '');
		if( $socialloginProfileData['BirthDate'] != "" ){
			switch( $socialloginProfileData['Provider'] ){
				case 'facebook':
				case 'foursquare':
				case 'yahoo':
				case 'openid':
				break;
				
				case 'google':
				$temp = explode( '/', $socialloginProfileData['BirthDate'] );  // yy/mm/dd
				$socialloginProfileData['BirthDate'] = $temp[1]."/".$temp[2]."/".$temp[0];
				break;
				
				case 'twitter':
				case 'linkedin':
				case 'vkontakte':
				case 'live';
				$temp = explode( '/', $socialloginProfileData['BirthDate'] );   // dd/mm/yy
				$socialloginProfileData['BirthDate'] = $temp[1]."/".$temp[0]."/".$temp[2];
				break;
			}
		}
		return $socialloginProfileData;
	}

	function socialLoginFilterAvatar( $id, $ImgUrl, $provider ){
		$thumbnail = (!empty($ImgUrl) ? trim($ImgUrl) : '');
		if (empty($thumbnail) && ( $provider == 'facebook' ) ) {
		  $thumbnail = "http://graph.facebook.com/" . $id . "/picture?type=large";
		}
		return $thumbnail;
	}
	/** 
	 * Validate url.
	 */
	function login_radius_validate_url($url){
		$validUrlExpression = "/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|)?[a-z0-9_\-]+[a-z0-9_\-\.]+\.[a-z]{2,4}(\/+[a-z0-9_\.\-\/]*)?$/i";
		return (bool)preg_match($validUrlExpression, $url);
	}
	function socialLoginUserLogin( $entityId, $socialId ) {
		$session = Mage::getSingleton("customer/session");
		$session->loginById($entityId);
		$session->setLoginRadiusId($socialId);
		$write_url = $this->blockObj->getCallBack();
		$Hover = $this->blockObj->getRedirectOption();
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		// check if logged in from callback page
		if(isset($_GET['loginradiuscheckout'])){
			$this -> redirect( Mage::helper('checkout/url')->getCheckoutUrl() );
			exit();
			return;
		}
		if($Hover == 'account'){
			$this -> redirect( $url.'customer/account' );
			exit();
			return;
		}elseif($Hover == 'index' ){
			$this -> redirect( $url.'') ;
			exit();
			return;
		}elseif( $Hover == 'custom' && $write_url != '' ) {
			$this -> redirect( $write_url.'' );
			exit();
			return;
		}else{
			 if(isset($_GET['redirect_to'])){
				$currentUrl = trim($_GET['redirect_to']);
			 }else{
				$currentUrl = $url;
			 }
			 $this -> redirect( $currentUrl);
			 exit();
			 return;
		}
	}

	function setInSession( $id, $socialloginProfileData ){
		$socialloginProfileData['lrId'] = $id;
		Mage::getSingleton('core/session')->setSocialLoginData( $socialloginProfileData );
	}

	function setTmpSession($loginRadiusPopupTxt = '', $socialLoginMsg = "", $loginRadiusShowForm = true, $profileData = array(), $emailRequired = true, $hideZipcode = false){
		Mage::getSingleton('core/session')->setTmpPopupTxt( $loginRadiusPopupTxt );
		Mage::getSingleton('core/session')->setTmpPopupMsg( $socialLoginMsg );
		Mage::getSingleton('core/session')->setTmpShowForm( $loginRadiusShowForm );
		Mage::getSingleton('core/session')->setTmpProfileData( $profileData );
		Mage::getSingleton('core/session')->setTmpEmailRequired( $emailRequired );
		Mage::getSingleton('core/session')->setTmpHideZipcode( $hideZipcode );
	}
	
	function loginRadiusEmail( $subject, $message, $to, $toName ){
		$storeName =  Mage::app()->getStore()->getGroup()->getName();
		$mail = new Zend_Mail('UTF-8'); //class for mail
		$mail->setBodyHtml( $message ); //for sending message containing html code
		$mail->setFrom( "Owner", $storeName );
		$mail->addTo( $to, $toName );
		//$mail->addCc($cc, $ccname);    //can set cc
		//$mail->addBCc($bcc, $bccname);    //can set bcc
		$mail->setSubject( $subject );
		try{
		  $mail->send();
		}catch(Exception $ex) {
		}
	}

	function socialLoginAddNewUser( $socialloginProfileData, $verify = false, $update = false, $customerId = '' ) {
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		if(!$update){
			// add new user magento way
			$customer = Mage::getModel("customer/customer");
		}else{
			$customer = Mage::getModel('customer/customer') -> load($customerId);
		}
		$customer->website_id = $websiteId; 
		$customer->setStore($store);
		if($socialloginProfileData['FirstName'] != ""){
			$customer->firstname = $socialloginProfileData['FirstName'];
		}
		if(!$update){
			$customer->lastname = $socialloginProfileData['LastName'] == "" ? $socialloginProfileData['FirstName'] : $socialloginProfileData['LastName'];
		}elseif($update && $socialloginProfileData['LastName'] != ""){
			$customer->lastname = $socialloginProfileData['LastName'];
		}
		if(!$update){
			$customer->email = $socialloginProfileData['Email'];
			$loginRadiusPwd = $customer->generatePassword(10);
			$customer->password_hash = md5( $loginRadiusPwd );
		}
		if($socialloginProfileData['BirthDate'] != ""){
			$customer->dob = $socialloginProfileData['BirthDate'];
		}
		if($socialloginProfileData['Gender'] != ""){
			$customer->gender = $socialloginProfileData['Gender'];
		}
		$customer->setConfirmation(null);
		$customer->save();
		
		// if updating user profile
		if($update){
			$addresses = $customer->getAddressesCollection();
			$matched = false;
			foreach($addresses as $address){
				$address = $address->toArray();
				if($address['firstname'] == $socialloginProfileData['FirstName']
				 	&& $address['lastname'] == $socialloginProfileData['LastName']
					&& $address['country_id'] == ucfirst($socialloginProfileData['Country'])
					&& $address['city'] == ucfirst($socialloginProfileData['City'])
					&& $address['telephone'] == $socialloginProfileData['PhoneNumber']
					&& $address['company'] == ucfirst($socialloginProfileData['Industry'])
					&& $address['street'] == ucfirst($socialloginProfileData['Address'])){
						$matched = true;
						// if profile data contains zipcode then match it with that in the address
						if(isset($socialloginProfileData['Zipcode']) && $address['postcode'] != $socialloginProfileData['Zipcode']){
							$matched = false;
						}
						// if profile data contains province then match it with that in the address
						if(isset($socialloginProfileData['Province']) && $address['region'] != $socialloginProfileData['Province']){
							$matched = false;
						}
				}
				if($matched){
					break;
				}
			}
		}
		$address = Mage::getModel("customer/address");
		if(!$update){
			$address->setCustomerId($customer->getId());
		}else{
			$address->setCustomerId($customerId);
		}
		if(($update && !$matched) || !$update){
			$address->firstname = $customer->firstname;
			$address->lastname = $customer->lastname;
			$address->country_id = isset($socialloginProfileData['Country']) ? ucfirst($socialloginProfileData['Country']) : '';
			if(isset($socialloginProfileData['Zipcode'])){
				$address->postcode = $socialloginProfileData['Zipcode'];
			}
			$address->city = isset($socialloginProfileData['City']) ? ucfirst($socialloginProfileData['City']) : '';
			// If country is USA, set up province
			if(isset($socialloginProfileData['Province'])){
				$address->region = $socialloginProfileData['Province'];
			}
			$address->telephone = isset($socialloginProfileData['PhoneNumber']) ? ucfirst($socialloginProfileData['PhoneNumber']) : '';
			$address->company = isset($socialloginProfileData['Industry']) ? ucfirst($socialloginProfileData['Industry']) : '';
			$address->street = isset($socialloginProfileData['Address']) ? ucfirst($socialloginProfileData['Address']) : '';
			// set default billing, shipping address and save in address book
			$address -> setIsDefaultShipping('1') -> setIsDefaultBilling('1') -> setSaveInAddressBook('1');
			$address->save();
		}
		// add info in sociallogin table
		if( !$verify ){
			$fields = array();
			$fields['sociallogin_id'] = $socialloginProfileData['lrId'] ;
			$fields['entity_id'] = $customer->getId();
			$fields['avatar'] = $socialloginProfileData['thumbnail'] ;
			$fields['provider'] = $socialloginProfileData['Provider'] ;
			if(!$update){
				$this->SocialLoginInsert( "sociallogin", $fields );
			}else{
				$this->SocialLoginInsert( "sociallogin", array('avatar' => $socialloginProfileData['thumbnail']), true, array('entity_id = ?' => $customerId) );
			}
			if(!$update){
				$loginRadiusUsername = $socialloginProfileData['FirstName']." ".$socialloginProfileData['LastName'];
				// email notification to user
				if( $this->blockObj->notifyUser() == "1" ){
					$loginRadiusMessage = $this->blockObj->notifyUserText();
					if( $loginRadiusMessage == "" ){
						$loginRadiusMessage = __("Welcome to ").$store->getGroup()->getName().". ".__("You can login to the store using following e-mail address and password");
					}
					$loginRadiusMessage .= "<br/>".
										   "Email : ".$socialloginProfileData['Email'].
										   "<br/>".__("Password")." : ".$loginRadiusPwd;
										   
					$this->loginRadiusEmail( __("Welcome")." ".$loginRadiusUsername."!", $loginRadiusMessage, $socialloginProfileData['Email'], $loginRadiusUsername );
				}
				// new user notification to admin
				if( $this->blockObj->notifyAdmin() == "1" ){
					$loginRadiusAdminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
					$loginRadiusAdminName = Mage::getStoreConfig('trans_email/ident_general/name');
					$loginRadiusMessage = trim($this->blockObj->notifyAdminText());
					if( $loginRadiusMessage == "" ){
						$loginRadiusMessage = __("New customer has been registered to your store with following details");
					}
					$loginRadiusMessage .= "<br/>".
										   __("Name")." : ".$loginRadiusUsername."<br/>".
										   __("Email")." : ".$socialloginProfileData['Email'];
					$this->loginRadiusEmail( __("New User Registration"), $loginRadiusMessage, $loginRadiusAdminEmail, $loginRadiusAdminName );
				}
			}
			//login and redirect user
			$this->socialLoginUserLogin( $customer->getId(), $fields['sociallogin_id'] );
		}
		if( $verify ){
			$this->verifyUser( $socialloginProfileData['lrId'], $customer->getId(), $socialloginProfileData['thumbnail'], $socialloginProfileData['Provider'], $socialloginProfileData['Email'] );
		}
	}

	private function SocialLoginInsert( $lrTable, $lrInsertData, $update = false, $value = '' ){
		$connection = Mage::getSingleton('core/resource')
							->getConnection('core_write');
		$connection->beginTransaction();
		$sociallogin = getMazeTable($lrTable);
		if( !$update ){
			$connection->insert($sociallogin, $lrInsertData);
		}else{
			// update query magento way
			$connection->update(
				$sociallogin,
				$lrInsertData,
				$value
			);
		}
		$connection->commit();
	}

	private function SocialLoginShowLayout() {
		$this->loadLayout();     
		$this->renderLayout();
	}

	private function loginRadiusRead( $table, $handle, $params, $result = false ){
		$socialLoginConn = Mage::getSingleton('core/resource')
							->getConnection('core_read');
		$Tbl = getMazeTable($table); 
		$websiteId = Mage::app()->getWebsite()->getId();
		$storeId = Mage::app()->getStore()->getId();
		$query = "";
		switch( $handle ){
			case "email exists pop1":
			$query = "select entity_id from $Tbl where email = '".$params[0]."' and website_id = $websiteId and store_id = $storeId";
			break;
			case "get user":
			$query = "select entity_id, verified from $Tbl where sociallogin_id= '".$params[0]."'";
			break;
			case "get user2":
			$query = "select entity_id from $Tbl where entity_id = ".$params[0]." and website_id = $websiteId and store_id = $storeId";
			break;
			case "email exists login":
			$query = "select * from $Tbl where email = '".$params[0]."' and website_id = $websiteId and store_id = $storeId";
			break;
			case "email exists sl":
			$query = "select verified,sociallogin_id from $Tbl where entity_id = '".$params[0]."' and provider = '".$params[1]."'";
			break;
			case "provider exists in sociallogin":
			$query = "select entity_id from $Tbl where entity_id = '".$params[0]."' and provider = '".$params[1]."'";
			break;
			case "verification":
			$query = "select entity_id, provider from $Tbl where vkey = '".$params[0]."'";
			break;
			case "verification2":
			$query = "select entity_id from $Tbl where entity_id = ".$params[0]." and provider = '".$params[1]."' and vkey != '' ";
			break;
		}
		$select = $socialLoginConn->query($query);
		if( $result ){
			return $select;
		}
		if( $rowArray = $select->fetch() ) { 
			return true;
		}
		return false;
	}
	
	private function verifyUser( $slId, $entityId, $avatar, $provider, $email ){
		$vKey = md5(uniqid(rand(), TRUE));
		$data = array();
		$data['sociallogin_id'] = $slId;
		$data['entity_id'] = $entityId;
		$data['avatar'] = $avatar;
		$data['verified'] = "0";
		$data['vkey'] = $vKey;
		$data['provider'] = $provider;
		// insert details in sociallogin table
		$this->SocialLoginInsert( "sociallogin", $data );
		// send verification mail
		$message = __(Mage::helper('core')->htmlEscape(trim($this->blockObj->verificationText())));
		if( $message == "" ){
			$message = __("Please click on the following link or paste it in browser to verify your email");
		}
		$message .= "<br/>".Mage::getBaseUrl()."sociallogin/?loginRadiusKey=".$vKey;
		$this->loginRadiusEmail( __("Email verification"), $message, $email, $email);
		$this -> setTmpSession( "Confirmation link has been sent to your email address. Please verify your email by clicking on confirmation link.", "", false );
		// show popup message
		$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
		$this->SocialLoginShowLayout();
		return;
	}
	
   	public function indexAction() {
		$this->blockObj = new Loginradius_Sociallogin_Block_Sociallogin();
		$this->loginRadiusPopMsg = trim($this->blockObj->getPopupText() );
		$this->loginRadiusPopMsg = $this->loginRadiusPopMsg == "" ? __("Please enter your email to proceed") : $this->loginRadiusPopMsg;
		$this->loginRadiusPopErr = trim($this->blockObj->getPopupError() );
		$this->loginRadiusPopErr = $this->loginRadiusPopErr == "" ? __("Email you entered is either invalid or already registered. Please enter a valid email.") : $this->loginRadiusPopErr;
		if(isset($_REQUEST['token'])) {
			$this->tokenHandle();
			$this->loadLayout();     
			$this->renderLayout();
			return;
		}
		
		// email verification
		if( isset($_GET['loginRadiusKey']) && !empty($_GET['loginRadiusKey']) ){
			$loginRadiusVkey = trim( $_GET['loginRadiusKey'] );
			// get entity_id and provider of the vKey
			$result = $this->loginRadiusRead( "sociallogin", "verification", array( $loginRadiusVkey ), true );
			if( $temp = $result->fetch() ){
				// set verified status true at this verification key
				$tempUpdate = array("verified" => '1', "vkey" => '');
				$tempUpdate2 = array("vkey = ?" => $loginRadiusVkey);
				$this->SocialLoginInsert( "sociallogin", $tempUpdate, true, $tempUpdate2 );
				$this -> setTmpSession("Your email has been verified. Now you can login to your account.", "", false );
				$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
				
				// check if verification for same provider is still pending on this entity_id
				if( $this->loginRadiusRead( "sociallogin", "verification2", array( $temp['entity_id'], $temp['provider'] ) ) ){
					$tempUpdate = array("vkey" => '');
					$tempUpdate2 = array("entity_id = ?" => $temp['entity_id'], "provider = ?" => $temp['provider']);
					$this->SocialLoginInsert( "sociallogin", $tempUpdate, true, $tempUpdate2 );
				}
			}
		}

		$socialLoginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
		$session_user_id = $socialLoginProfileData['lrId'];
		$loginRadiusPopProvider = $socialLoginProfileData['Provider'];
		$loginRadiusAvatar = $socialLoginProfileData['thumbnail'];
		// popup check
		if(isset($_GET['loginradiuspopup'])){
			SL_popUpWindow( Mage::getSingleton('core/session')->getTmpPopupTxt(), Mage::getSingleton('core/session')->getTmpPopupMsg(), Mage::getSingleton('core/session')->getTmpShowForm(), Mage::getSingleton('core/session')->getTmpProfileData(), Mage::getSingleton('core/session')->getTmpEmailRequired(), Mage::getSingleton('core/session')->getTmpHideZipcode());
			$this -> SocialLoginShowLayout();
			return;
		}
		if(isset($_POST['LoginRadiusRedSliderClick'])) {
			if(!empty($session_user_id) ){
				$loginRadiusProfileData = array();
				// address
				if(isset($_POST['loginRadiusAddress'])){
					$loginRadiusProfileData['Address'] = "";
					$profileAddress = trim($_POST['loginRadiusAddress']);
				}
				// city
				if(isset($_POST['loginRadiusCity'])){
					$loginRadiusProfileData['City'] = "";
					$profileCity = trim($_POST['loginRadiusCity']);
				}
				// country
				if(isset($_POST['loginRadiusCountry'])){
					$loginRadiusProfileData['Country'] = "";
					$profileCountry = trim($_POST['loginRadiusCountry']);
				}
				// phone number
				if(isset($_POST['loginRadiusPhone'])){
					$loginRadiusProfileData['PhoneNumber'] = "";
					$profilePhone = trim($_POST['loginRadiusPhone']);
				}
				// email
				if(isset($_POST['loginRadiusEmail'])){
					$email = trim($_POST['loginRadiusEmail']);
					if( !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email) ){
						if($this->blockObj->getProfileFieldsRequired() == 1){
							$hideZipCountry = false;
						}else{
							$hideZipCountry = true;
						}
						$this -> setTmpSession($this->loginRadiusPopMsg, $this->loginRadiusPopErr, true, $loginRadiusProfileData, true, $hideZipCountry);
						$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
						$this->SocialLoginShowLayout();
						return false;
					}
					// check if email already exists
					$userId = $this->loginRadiusRead( "customer_entity", "email exists pop1", array($email), true );
					if( $rowArray = $userId->fetch() ) {  // email exists
						//check if entry exists on same provider in sociallogin table
						$verified = $this->loginRadiusRead( "sociallogin", "email exists sl", array( $rowArray['entity_id'], $loginRadiusPopProvider ), true );
						if( $rowArray2 = $verified->fetch() ){
							// check verified field
							if( $rowArray2['verified'] == "1" ){
								// check sociallogin id
								if( $rowArray2['sociallogin_id'] == $session_user_id ){
									$this->socialLoginUserLogin( $rowArray['entity_id'], $rowArray2['sociallogin_id'] );
									return;
								}else{
									$this -> setTmpSession($this->loginRadiusPopMsg, $this->loginRadiusPopErr, true, array(), true, true);
									$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
									$this->SocialLoginShowLayout();
									return;
								}
							}else{
								// check sociallogin id
								if( $rowArray2['sociallogin_id'] == $session_user_id ){
									$this -> setTmpSession("Please provide following details", "", true, $socialloginProfileData, false);
									$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
									$this->SocialLoginShowLayout();
									return;
								}else{
									// send verification email
									$this->verifyUser( $session_user_id, $rowArray['entity_id'], $loginRadiusAvatar, $loginRadiusPopProvider, $email );
									return;
								}
							}
						}else{
							// send verification email
							$this->verifyUser( $session_user_id, $rowArray['entity_id'], $loginRadiusAvatar, $loginRadiusPopProvider, $email );
							return;
						}
					}
				}
				// validate other profile fields
				if((isset($profileAddress) && $profileAddress == "") || (isset($profileCity) && $profileCity == "") || (isset($profileCountry) && $profileCountry == "") || (isset($profilePhone) && $profilePhone == "")){
					$this -> setTmpSession("", "Please fill all the fields", true, $loginRadiusProfileData, true);
					$this -> redirect(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK)."sociallogin?loginradiuspopup=1");
					$this->SocialLoginShowLayout();
					return false;
				}
				$socialloginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
				// assign submitted profile fields to array
				// address
				if(isset($profileAddress)){
					$socialloginProfileData['Address'] = $profileAddress;
				}
				// city
				if(isset($profileCity)){
					$socialloginProfileData['City'] = $profileCity;
				}
				// Country
				if(isset($profileCountry)){
					$socialloginProfileData['Country'] = $profileCountry;
				}
				// Phone Number
				if(isset($profilePhone)){
					$socialloginProfileData['PhoneNumber'] = $profilePhone;
				}
				// Zipcode
				if(isset($_POST['loginRadiusZipcode'])){
					$socialloginProfileData['Zipcode'] = trim($_POST['loginRadiusZipcode']);
				}
				// Province
				if(isset($_POST['loginRadiusProvince'])){
					$socialloginProfileData['Province'] = trim($_POST['loginRadiusProvince']);
				}
				// Email
				if(isset($email)){
					$socialloginProfileData['Email'] = $email;
					$verify = true;
				}else{
					$verify = false;
				}
				Mage::getSingleton('core/session')->unsSocialLoginData(); 	// unset session
				$this->socialLoginAddNewUser( $socialloginProfileData, $verify ); 
			}
		}elseif( isset($_POST['LoginRadiusPopupCancel']) ) { 				// popup cancelled
			Mage::getSingleton('core/session')->unsSocialLoginData(); 		// unset session
			
			Mage::getSingleton('core/session')->unsTmpPopupTxt();
			Mage::getSingleton('core/session')->unsTmpPopupMsg();
			Mage::getSingleton('core/session')->unsTmpShowForm();
			Mage::getSingleton('core/session')->unsTmpProfileData();
			Mage::getSingleton('core/session')->unsTmpEmailRequired();
			Mage::getSingleton('core/session')->unsTmpHideZipcode();
			
			$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
			$this -> redirect($url);										// redirect to index page
		}
		$this->SocialLoginShowLayout();
    }
	
	/**
	 * Action for AJAX
	 */
	 public function ajaxAction(){
	 	$this->loadLayout();
    	$this->renderLayout();
	 }
}