<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 *  sociallogin controller
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_IndexController this is the controller where loginradius login and registration takes place
 */
class Loginradius_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action
{
    public $socialloginProfileData;
    public $blockObj;
    private $loginRadiusPopMsg;
    private $loginRadiusPopErr;
    private $loginRadiusToken;

    /**
     * Default action for LoginRadius sociallogin controller
     */
    public function indexAction()
    {
        $this->loginRadiusToken = $this->getRequest()->getPost('token');
        $this->blockObj = new Loginradius_Sociallogin_Block_Sociallogin();
        $this->loginRadiusPopMsg = trim($this->blockObj->getPopupText());
        $this->loginRadiusPopErr = trim($this->blockObj->getPopupError());
        if (!empty($this->loginRadiusToken)) {
            $this->tokenHandler();
        } else {
            $this->popupHandler();
        }

    }

    /**
     * This function handles all the process once got the token from LoginRadius
     */
    public function tokenHandler()
    {
        $loginHelper = Mage::helper('sociallogin/loginhelper');
        $loginRadiusSdk = Mage::helper('sociallogin/loginradiussdk');
        // Fetch user profile using access token ......
        $responseFromLoginRadius = $loginRadiusSdk->fetchUserProfile($this->loginRadiusToken);
        $userObj = json_decode($responseFromLoginRadius);

        if (is_object($userObj) && isset($userObj->ID) && !empty($userObj->ID)) {
            $this->socialloginProfileData = $loginHelper->socialLoginFilterData($userObj);
            $this->socialloginProfileData['lrToken'] = $this->loginRadiusToken;
            // If linking variable is available then link account
            if ($loginHelper->isAlreadyLoggedIn()) {
                $loginHelper->loginRadiusSocialLinking(Mage::getSingleton("customer/session")->getCustomer()->getId(), $userObj->ID, $userObj->Provider, $userObj->ThumbnailImageUrl, true);
            }
            //user not loggedin
            if (!$loginHelper->isAlreadyLoggedIn()) {
                //check user on database with social id

                $innerJoinQuery = $loginHelper->loginRadiusRead("sociallogin", "get_user_by_social_id", array($userObj->ID), true);
                if ($customerEntity = $innerJoinQuery->fetch()) {
                    if ($customerEntity['verified'] == "0") {
                        $session = Mage::getSingleton('customer/session');
                        $session->addError(__('Please verify your email to login.'));
                        header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
                        exit();
                    } else {
                        if ($this->blockObj->updateProfileData() != '1') {
                            $loginHelper->socialLoginUserLogin($customerEntity['entity_id'], $userObj->ID);
                        } else {
                            $this->socialloginProfileData['lrId'] = $userObj->ID;
                            $loginHelper->socialLoginAddNewUser($this->socialloginProfileData, false, true, $customerEntity['entity_id']);
                        }

                        return;
                    }
                } //Social Id not Exist in Local DB and email not empty
                elseif (isset($userObj->Email[0]->Value) && !empty($userObj->Email[0]->Value)) {
                    //if email is provided by provider then check if it's in table
                    $email = $userObj->Email[0]->Value;
                    $select = $loginHelper->loginRadiusRead("customer_entity", "email_already_exists", array($email), true);
                    if ($rowArray = $select->fetch()) {
                        //user is in customer table
                        if ($this->blockObj->getLinking() == "1") { // Social Linking
                            $loginHelper->loginRadiusSocialLinking($rowArray['entity_id'], $userObj->ID, $userObj->Provider, $userObj->ThumbnailImageUrl);
                        }

                        if ($this->blockObj->updateProfileData() != '1') {
                            $loginHelper->socialLoginUserLogin($rowArray['entity_id'], $userObj->ID);
                        } else {
                            $this->socialloginProfileData = $loginHelper->socialLoginFilterData($userObj);
                            $this->socialloginProfileData['lrId'] = $userObj->ID;
                            $loginHelper->socialLoginAddNewUser($this->socialloginProfileData, false, true, $rowArray['entity_id']);
                        }
                    } else {
                        $this->socialloginProfileData['lrId'] = $userObj->ID;
                        if ($this->blockObj->getProfileFieldsRequired() == 1) {
                            $loginHelper->setInSession($userObj->ID, $this->socialloginProfileData);
                            Mage::helper('sociallogin')->setTmpSession($this->blockObj->getPopupText(), "", true, $this->socialloginProfileData, false);
                            // show a popup to fill required profile fields
                            $this->getPopupTemplate();

                            return;
                        }
                        $loginHelper->socialLoginAddNewUser($this->socialloginProfileData);
                    }
                } else {
                    $emailRequired = true;
                    if ($this->blockObj->getEmailRequired() == 0) { // dummy email
                        $email = $loginHelper->getAutoGeneratedEmail($userObj);
                        $this->socialloginProfileData['Email'] = $email;
                        $this->socialloginProfileData['lrId'] = $userObj->ID;
                        $emailRequired = false;
                    }
                    //
                    $this->socialloginProfileData['lrToken'] = $this->loginRadiusToken;
                    //show required fields popup
                    $loginHelper->setInSession($userObj->ID, $this->socialloginProfileData);
                    if ($this->blockObj->getProfileFieldsRequired() == 1) {
                        // show a popup to fill required profile fields
                        Mage::helper('sociallogin')->setTmpSession($this->loginRadiusPopMsg, "", true, $this->socialloginProfileData, $emailRequired);
                        $this->getPopupTemplate();
                    } elseif ($this->blockObj->getEmailRequired() == 1) {
                        Mage::helper('sociallogin')->setTmpSession($this->loginRadiusPopMsg, "", true, $this->socialloginProfileData, $emailRequired, true);
                        $this->getPopupTemplate();
                    } else {
                        //create new user without showing popup
                        $loginHelper->socialLoginAddNewUser($this->socialloginProfileData);
                    }
                }
            }
        } else {
            if ($this->blockObj->isDebuggingOn()) {
                Mage::getSingleton('core/session')->addError($userObj->description);
            }
            $referrerUrl = $this->_getRefererUrl();
            if (empty($referrerUrl)) {
                $referrerUrl = Mage::getBaseUrl();
            }
            header("Location:" . $referrerUrl);
            exit();
        }
    }

    /**
     * Get template to render email required popup
     */
    public function getPopupTemplate()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('Mage_Core_Block_Template', 'emailpopup', array('template' => 'sociallogin/popup.phtml'))
        );
        $this->renderLayout();

        return $this;
    }

    /**
     * Function to display friend invite popup, delas with email poup submission, customer verification functionality.
     */
    public function popupHandler()
    {
        $loginHelper = Mage::helper('sociallogin/loginhelper');
        // email verification
        if (isset($_GET['loginRadiusKey']) && !empty($_GET['loginRadiusKey'])) {
            $loginRadiusVkey = strip_tags(trim($_GET['loginRadiusKey']));
            // get entity_id and provider of the vKey
            $result = $loginHelper->loginRadiusRead("sociallogin", "verification", array($loginRadiusVkey), true);
            if ($temp = $result->fetch()) {
                // set verified status true at this verification key
                $tempUpdate = array("verified" => '1', "vkey" => '');
                $tempUpdate2 = array("vkey = ?" => $loginRadiusVkey);
                $loginHelper->SocialLoginInsert("sociallogin", $tempUpdate, true, $tempUpdate2);

                // check if verification for same provider is still pending on this entity_id
                if ($loginHelper->loginRadiusRead("sociallogin", "verification2", array($temp['entity_id'], $temp['provider']))) {
                    $tempUpdate = array("vkey" => '');
                    $tempUpdate2 = array("entity_id = ?" => $temp['entity_id'], "provider = ?" => $temp['provider']);
                    $loginHelper->SocialLoginInsert("sociallogin", $tempUpdate, true, $tempUpdate2);
                }
                $session = Mage::getSingleton('core/session');
                $session->addSuccess(__('Your email has been verified. Now you can login to your account.'));
                header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
                exit();

            }
        }

        if (isset($_POST['EmailPopupOkButton'])) {
            $socialLoginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
            $session_user_id = $socialLoginProfileData['lrId'];
            $loginRadiusPopProvider = $socialLoginProfileData['Provider'];
            $loginRadiusAvatar = $socialLoginProfileData['Thumbnail'];
            if (!empty($session_user_id)) {
                $loginRadiusProfileData = array();
                // address
                if (isset($_POST['loginRadiusAddress'])) {
                    $loginRadiusProfileData['Address'] = "";
                    $profileAddress = strip_tags(trim($_POST['loginRadiusAddress']));
                }
                // city
                if (isset($_POST['loginRadiusCity'])) {
                    $loginRadiusProfileData['City'] = "";
                    $profileCity = strip_tags(trim($_POST['loginRadiusCity']));
                }
                // country
                if (isset($_POST['loginRadiusCountry'])) {
                    $loginRadiusProfileData['Country'] = "";
                    $profileCountry = strip_tags(trim($_POST['loginRadiusCountry']));
                }
                // phone number
                if (isset($_POST['loginRadiusPhone'])) {
                    $loginRadiusProfileData['PhoneNumber'] = "";
                    $profilePhone = strip_tags(trim($_POST['loginRadiusPhone']));
                }
                // email
                if (isset($_POST['loginRadiusEmail'])) {
                    $email = trim($_POST['loginRadiusEmail']);
                    $select = $loginHelper->loginRadiusRead("customer_entity", "email_already_exists", array($email), true);
                    if ($rowArray = $select->fetch()) {
                        $errorMessage = $this->blockObj->getPopupError();
                        if ($this->blockObj->getProfileFieldsRequired() == 1) {
                            Mage::helper('sociallogin')->setTmpSession("", $errorMessage, true, $socialLoginProfileData, true);
                        } else {
                            Mage::helper('sociallogin')->setTmpSession("", $errorMessage, true, $socialLoginProfileData, true, true);
                        }
                        $this->getPopupTemplate();

                        return;
                    }

                    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) {
                        if ($this->blockObj->getProfileFieldsRequired() == 1) {
                            $hideZipCountry = false;
                        } else {
                            $hideZipCountry = true;
                        }
                        Mage::helper('sociallogin')->setTmpSession($this->blockObj->getPopupText(), $this->blockObj->getPopupError(), true, $loginRadiusProfileData, true, $hideZipCountry);
                        $this->getPopupTemplate();

                        return;
                    }
                    // check if email already exists
                    $userId = $loginHelper->loginRadiusRead("customer_entity", "email exists pop1", array($email), true);
                    if ($rowArray = $userId->fetch()) { // email exists
                        //check if entry exists on same provider in sociallogin table
                        $verified = $loginHelper->loginRadiusRead("sociallogin", "email exists sl", array($rowArray['entity_id'], $loginRadiusPopProvider), true);
                        if ($rowArray2 = $verified->fetch()) {
                            // check verified field
                            if ($rowArray2['verified'] == "1") {
                                // check sociallogin id
                                if ($rowArray2['sociallogin_id'] == $session_user_id) {
                                    $loginHelper->socialLoginUserLogin($rowArray['entity_id'], $rowArray2['sociallogin_id']);
                                } else {
                                    Mage::helper('sociallogin')->setTmpSession($this->loginRadiusPopMsg, $this->loginRadiusPopErr, true, array(), true, true);
                                    $this->getPopupTemplate();
                                }

                                return;
                            } else {
                                // check sociallogin id
                                if ($rowArray2['sociallogin_id'] == $session_user_id) {
                                    Mage::helper('sociallogin')->setTmpSession("Please provide following details", "", true, $this->socialloginProfileData, false);
                                    $this->getPopupTemplate();
                                } else {
                                    // send verification email
                                    $loginHelper->verifyUser($session_user_id, $rowArray['entity_id'], $loginRadiusAvatar, $loginRadiusPopProvider, $email);
                                }

                                return;
                            }
                        } else {
                            // send verification email
                            $loginHelper->verifyUser($session_user_id, $rowArray['entity_id'], $loginRadiusAvatar, $loginRadiusPopProvider, $email);

                            return;
                        }
                    }
                }
                // validate other profile fields
                if ((isset($profileAddress) && $profileAddress == "") || (isset($profileCity) && $profileCity == "") || (isset($profileCountry) && $profileCountry == "") || (isset($profilePhone) && $profilePhone == "")) {
                    Mage::helper('sociallogin')->setTmpSession("", "Please fill all the fields", true, $loginRadiusProfileData, true);
                    $this->popupHandle();

                    return;
                }
                $this->socialloginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
                // assign submitted profile fields to array
                // address
                if (isset($profileAddress)) {
                    $this->socialloginProfileData['Address'] = $profileAddress;
                }
                // city
                if (isset($profileCity)) {
                    $this->socialloginProfileData['City'] = $profileCity;
                }
                // Country
                if (isset($profileCountry)) {
                    $this->socialloginProfileData['Country'] = $profileCountry;
                }
                // Phone Number
                if (isset($profilePhone)) {
                    $this->socialloginProfileData['PhoneNumber'] = $profilePhone;
                }
                // Zipcode
                if (isset($_POST['loginRadiusZipcode'])) {
                    $this->socialloginProfileData['Zipcode'] = trim($_POST['loginRadiusZipcode']);
                }
                // Province
                if (isset($_POST['loginRadiusProvince'])) {
                    $this->socialloginProfileData['Province'] = trim($_POST['loginRadiusProvince']);
                }
                // Email
                if (isset($email)) {
                    $this->socialloginProfileData['Email'] = $email;
                    $verify = true;
                } else {
                    $verify = false;
                }
                Mage::getSingleton('core/session')->unsSocialLoginData(); // unset session
                if ($this->blockObj->getProfileFieldsRequired() == "1") {
                    $loginHelper->socialLoginAddNewUser($this->socialloginProfileData, $verify, false, '', true);
                } else {
                    $loginHelper->socialLoginAddNewUser($this->socialloginProfileData, $verify);
                }

            }
        } elseif (isset($_POST['LoginRadiusPopupCancel'])) { // popup cancelled
            Mage::getSingleton('core/session')->unsSocialLoginData(); // unset session

            Mage::getSingleton('core/session')->unsTmpPopupTxt();
            Mage::getSingleton('core/session')->unsTmpPopupMsg();
            Mage::getSingleton('core/session')->unsTmpShowForm();
            Mage::getSingleton('core/session')->unsTmpProfileData();
            Mage::getSingleton('core/session')->unsTmpEmailRequired();
            Mage::getSingleton('core/session')->unsTmpHideZipcode();

            header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
            exit();
        }
        $this->loadLayout();
        $this->renderLayout();

    }

    /**
     * Override _getSession method
     */
    protected function _getSession()
    {
        return Mage::getSingleton('sociallogin/session');
    }


}