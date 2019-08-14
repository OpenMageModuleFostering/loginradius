<?php
Mage::app('default');
include_once("inc.php");//sdk file

//customer will be re-directed to this file. this file handle all token, email etc things.

class Loginradius_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action
{

	protected function _getSession(){
		return Mage::getSingleton('sociallogin/session');
	}
	
	//if token is posted then this function will be called. It will login user if already in database. else if email is provided by api, it will insert data and login user. It will handle all after token.
	
	function tokenHandle() {
		$blockObj = new Loginradius_Sociallogin_Block_Sociallogin();//object to get api key and secrets, url etc
		$ApiSecrete = $blockObj->getApiSecret();
		
		$user_obj = $blockObj->getProfileResult($ApiSecrete);
		
		$id= $user_obj->ID;
		if(empty($id)){
			//invalid user
			return;
		}
		
		//valid user, checking if user in database?
		$connection = Mage::getSingleton('core/resource')
							->getConnection('core_read');
		$tbl_sociallog = getMazeTable("sociallogin");   // mage_sociallogin
		$customerTbl = getMazeTable("customer_entity");  // customer_entity
		
		$websiteId = Mage::app()->getWebsite()->getId();
		$storeId = Mage::app()->getStore()->getId();
		
		$socialLoginIdResult = $connection->query("select entity_id from $tbl_sociallog where sociallogin_id= '$id'");
		$socialLoginIds = $socialLoginIdResult->fetchAll();
		foreach( $socialLoginIds as $socialLoginId ){
			$select = $connection->query("select entity_id from $customerTbl where entity_id = ".$socialLoginId['entity_id']." and website_id = $websiteId and store_id = $storeId" );
			if($rowArray = $select->fetch()){
				break;
			}
		}
		$sociallogin_id = $rowArray['entity_id'];
		if(!empty($sociallogin_id)){//user is in database
			$this->socialLoginUserLogin( $sociallogin_id, $blockObj);
			return;
		}
		
		if( !empty($user_obj->Email) ){
			//if email is provided by provider then check if it's in table
			$email = $user_obj->Email['0']->Value;
			$select = $connection->query("select * from $customerTbl where email = '$email' and website_id = $websiteId and store_id = $storeId");
			
			
			if( $rowArray = $select->fetch() ) {
				$sociallogin_id = $rowArray['entity_id'];
				
				if(!empty($sociallogin_id)) {
					//user is in customer table
					$this->socialLoginUserLogin( $sociallogin_id, $blockObj);
					return;
				}
			}
			
			$socialloginProfileData = $this->socialLoginFilterData( $email, $user_obj );
			$socialloginProfileData['lrId'] = $user_obj->ID;
			$this->socialLoginAddNewUser( $socialloginProfileData, $email, $blockObj );
			return;
		}
		// empty email
		if( $blockObj->getEmailRequired() == 0 ) { 	// dummy email
			$email = $this->loginradius_get_randomEmail( $user_obj );
			$socialloginProfileData = $this->socialLoginFilterData( $email, $user_obj );
			$socialloginProfileData['lrId'] = $user_obj->ID;
			$this->socialLoginAddNewUser( $socialloginProfileData, $email, $blockObj );
			return;
		}else {		// show popup
			$id = $user_obj->ID;
			$socialloginProfileData = $this->socialLoginFilterData( $email, $user_obj );
			$this->setInSession($id, $socialloginProfileData);
			SL_popUpWindow();
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
	
	function socialLoginFilterData( $email, $user_obj ) {
		$socialloginProfileData = array();
		
		$socialloginProfileData['Provider'] = empty($user_obj->Provider) ? "" : $user_obj->Provider;
		$socialloginProfileData['FirstName'] = empty($user_obj->FirstName) ? "" : $user_obj->FirstName;
		$socialloginProfileData['FullName'] = empty($user_obj->FullName) ? "" : $user_obj->FullName;
		$socialloginProfileData['NickName'] = empty($user_obj->NickName) ? "" : $user_obj->NickName;
		$socialloginProfileData['LastName'] = empty($user_obj->LastName) ? "" : $user_obj->LastName;
		$socialloginProfileData['Addresses'] = empty($user_obj->Addresses['0']->Address1) ? "" : $user_obj->Addresses['0']->Address1;
		$socialloginProfileData['PhoneNumbers'] = empty( $user_obj->PhoneNumbers['0']->PhoneNumber ) ? "" : $user_obj->PhoneNumbers['0']->PhoneNumber;
		$socialloginProfileData['State'] = empty($user_obj->State) ? "" : $user_obj->State;
		$socialloginProfileData['City'] = empty($user_obj->City) ? "" : $user_obj->City;
		$socialloginProfileData['Industry'] = empty($user_obj->Positions['0']->Comapny->Name) ? "" : $user_obj->Positions['0']->Comapny->Name;
		$socialloginProfileData['Country'] = empty($user_obj->Country) ? "" : $user_obj->Country;
		
		$explode= explode("@",$email);
		if( empty( $socialloginProfileData['FirstName'] ) && !empty( $socialloginProfileData['FullName'] ) ){
			$socialloginProfileData['FirstName'] = $socialloginProfileData['FullName'];
		}elseif(empty($socialloginProfileData['FirstName'] ) && !empty( $socialloginProfileData['NickName'] )){
			$socialloginProfileData['FirstName'] = $socialloginProfileData['NickName'];
		}elseif(empty($socialloginProfileData['FirstName'] ) && empty($socialloginProfileData['NickName'] ) && !empty($socialloginProfileData['FullName'] ) ){
			$socialloginProfileData['FirstName'] = $explode[0];
		}
		if($socialloginProfileData['FirstName'] == '' ){
			$letters = range('a', 'z');
			for($i=0;$i<5;$i++){
				$socialloginProfileData['FirstName'] .= $letters[rand(0,26)];
			}
		}

		return $socialloginProfileData;
	}
	
	function socialLoginUserLogin( $sociallogin_id, $blockObj ) {
		$session = Mage::getSingleton("customer/session");
		$session->loginById($sociallogin_id);
		$write_url = $blockObj->getCallBack();
		$Hover = $blockObj->getRedirectOption();
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		if($Hover=='account' ){
			header( 'Location: '.$url.'customer/account' );
			return;
		}elseif($Hover=='index' ){
			header( 'Location: '.$url.'') ;
			return;
		}elseif( $Hover=='custom' && $write_url != '' ) {
			header( 'Location: '.$write_url.'' );
			return;
		} else {
			 $currentUrl = Mage::helper('core/url')->getCurrentUrl();
			 header( 'Location: '.$currentUrl.'' );
			 return;
		}
	}
	
	function setInSession( $id, $socialloginProfileData ){
		$socialloginProfileData['lrId'] = $id;
		Mage::getSingleton('core/session')->setSocialLoginData( $socialloginProfileData );
	}
	
	function socialLoginAddNewUser( $socialloginProfileData, $email, $blockObj ) {
		// add new user magento way
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		 
		$customer = Mage::getModel("customer/customer");
		$customer->website_id = $websiteId; 
		$customer->setStore($store);
		 
		$customer->firstname = $socialloginProfileData['FirstName'];
		$customer->lastname = $socialloginProfileData['LastName'];
		$customer->email = $email;
		
		$customer->password_hash = md5( $customer->generatePassword(10) );
		$customer->save();
		 
		//$address = new Mage_Customer_Model_Address();
		$address = Mage::getModel("customer/address");
		$address->setCustomerId($customer->getId());
		$address->firstname = $customer->firstname;
		$address->lastname = $customer->lastname;
		$address->country_id = ucfirst( $socialloginProfileData['Country'] ); //Country code here
		// $address->postcode = "31000";
		$address->city = ucfirst( $socialloginProfileData['City'] );
		/* NOTE: If country is USA, please set up $address->region also */
		$address->telephone = $socialloginProfileData['PhoneNumbers'];
		$address->company = ucfirst( $socialloginProfileData['Industry'] );
		$address->street = ucfirst( $socialloginProfileData['Addresses'] );
		 
		$address->save();
		
		// add info in sociallogin table
		$connection = Mage::getSingleton('core/resource')
		->getConnection('core_write');
		$connection->beginTransaction();
		$fields = array();
		$fields['sociallogin_id'] = $socialloginProfileData['lrId'] ;
		$fields['entity_id'] = $customer->getId();
		$sociallogin = getMazeTable("sociallogin");

		$connection->insert($sociallogin, $fields);
		
		$connection->commit();
		
		//login user
		$this->socialLoginUserLogin( $customer->getId(), $blockObj );
	}
	
	private function SocialLoginShowLayout() {
		$this->loadLayout();     
		$this->renderLayout();
	}
	
   	public function indexAction() {
		if(isset($_REQUEST['token'])) {
			$this->tokenHandle();
			$this->loadLayout();     
			$this->renderLayout();
			return;
		}
		
		$socialLoginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
		$session_user_id = $socialLoginProfileData['lrId'];
		
		if( isset($_POST['LoginRadiusRedSliderClick']) ) {
			if( !empty($session_user_id) ){
				$email = trim($_POST['SL_EMAIL']);
				
				if( !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email) ){
					SL_popUpWindow( "Please enter a valid email address" );
					$this->SocialLoginShowLayout();
					return false;
				}
				
				// check if email already exists
				$socialLoginConn = Mage::getSingleton('core/resource')
									->getConnection('core_read');
				$customerTbl = getMazeTable("customer_entity");  // customer_entity
				$websiteId = Mage::app()->getWebsite()->getId();
				$storeId = Mage::app()->getStore()->getId();

				$select = $socialLoginConn->query("select entity_id from $customerTbl where email = '$email' and website_id = $websiteId and store_id = $storeId");
				
				if( $rowArray = $select->fetch() ) {  // email exists
					SL_popUpWindow( "This email already exists. Please enter valid email address." );
					$this->SocialLoginShowLayout();
					return false;
				}
				
				$socialloginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
				Mage::getSingleton('core/session')->unsSocialLoginData(); 	// unset session
				
				$blockObj = new Loginradius_Sociallogin_Block_Sociallogin();	//object to get api key and secrets, url etc
				$this->socialLoginAddNewUser($socialloginProfileData, $email, $blockObj);
			}
		}elseif( isset($_POST['LoginRadiusPopupCancel']) ) { 		// popup cancelled
			Mage::getSingleton('core/session')->unsSocialLoginData(); 		// unset session
			$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
			header("Location:".$url);		// redirect to index page
		}
		$this->SocialLoginShowLayout();
    }
}