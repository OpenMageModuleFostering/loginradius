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
 *  sociallogin loginhelper helper
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_Helper_LoginHelper which contains functions related tosocail login process functionality
 */
class Loginradius_Sociallogin_Helper_Loginhelper extends Mage_Core_Helper_Abstract
{

    /**
     * function responsible for customer registration and profile updation
     *
     * @param        $socialloginProfileData
     * @param bool   $verify
     * @param bool   $update
     * @param string $customerId
     * @param bool   $requiredFields
     */
    public function socialLoginAddNewUser($socialloginProfileData, $verify = false, $update = false, $customerId = '', $requiredFields = false)
    {
        $blockObject = new Loginradius_Sociallogin_Block_Sociallogin();
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        if (!$update) {
            $redirectionTo = 'Registration';
            // add new user magento way
            $customer = Mage::getModel("customer/customer");
        } else {
            $redirectionTo = 'Login';
            $customer = Mage::getModel('customer/customer')->load($customerId);
        }
        $customer->website_id = $websiteId;
        $customer->setStore($store);
        if ($socialloginProfileData['FirstName'] != "") {
            $customer->firstname = $socialloginProfileData['FirstName'];
        }
        if (!$update) {
            $customer->lastname = $socialloginProfileData['LastName'] == "" ? $socialloginProfileData['FirstName'] : $socialloginProfileData['LastName'];
        } elseif ($update && $socialloginProfileData['LastName'] != "") {
            $customer->lastname = $socialloginProfileData['LastName'];
        }
        if (!$update) {
            $customer->email = $socialloginProfileData['Email'];
            $loginRadiusPwd = $customer->generatePassword(10);
            $customer->password_hash = md5($loginRadiusPwd);
        }
        if ($socialloginProfileData['BirthDate'] != "") {
            $customer->dob = $socialloginProfileData['BirthDate'];
        }
        if ($socialloginProfileData['Gender'] != "") {
            $customer->gender = $socialloginProfileData['Gender'];
        }
        $customer->setConfirmation(null);
        $customer->save();

        $address = Mage::getModel("customer/address");
        if (!$update) {
            $address->setCustomerId($customer->getId());
        }
        if (!$update) {
            $address->firstname = $customer->firstname;
            $address->lastname = $customer->lastname;
            $address->country_id = isset($socialloginProfileData['Country']) ? ucfirst($socialloginProfileData['Country']) : '';
            if (isset($socialloginProfileData['Zipcode'])) {
                $address->postcode = $socialloginProfileData['Zipcode'];
            }
            $address->city = isset($socialloginProfileData['City']) ? ucfirst($socialloginProfileData['City']) : '';
            if (isset($socialloginProfileData['State']) && !empty($socialloginProfileData['State'])) {
                $address->region = $socialloginProfileData['State'];
            }
            // If country is USA, set up province
            if (isset($socialloginProfileData['Province'])) {
                $address->region = $socialloginProfileData['Province'];
            }
            $address->telephone = isset($socialloginProfileData['PhoneNumber']) ? ucfirst($socialloginProfileData['PhoneNumber']) : '';
            $address->company = isset($socialloginProfileData['Industry']) ? ucfirst($socialloginProfileData['Industry']) : '';
            $address->street = isset($socialloginProfileData['Address']) ? ucfirst($socialloginProfileData['Address']) : '';
            // set default billing, shipping address and save in address book
            $address->setIsDefaultShipping('1')->setIsDefaultBilling('1')->setSaveInAddressBook('1');
            if ($requiredFields) {
                $address->save();
            }
        }
        // add info in sociallogin table
        if (!$verify) {
            $fields = array();
            $fields['sociallogin_id'] = $socialloginProfileData['lrId'];
            $fields['entity_id'] = $customer->getId();
            $fields['avatar'] = $socialloginProfileData['Thumbnail'];
            $fields['provider'] = $socialloginProfileData['Provider'];
            if (!$update) {
                $this->SocialLoginInsert("sociallogin", $fields);
            } else {
                $this->SocialLoginInsert("sociallogin", array('avatar' => $socialloginProfileData['thumbnail']), true, array('entity_id = ?' => $customerId));
            }
            if (!$update) {
                $loginRadiusUsername = $socialloginProfileData['FirstName'] . " " . $socialloginProfileData['LastName'];
                // email notification to user
                if ($blockObject->notifyUser() == "1") {
                    $loginRadiusMessage = $blockObject->notifyUserText();
                    if ($loginRadiusMessage == "") {
                        $loginRadiusMessage = __("Welcome to ") . $store->getGroup()->getName() . ". " . __("You can login to the store using following e-mail address and password");
                    }
                    $loginRadiusMessage .= "<br/>" . "Email : " . $socialloginProfileData['Email'] . "<br/>" . __("Password") . " : " . $loginRadiusPwd;

                    Mage::helper('sociallogin/loginhelper')->loginRadiusEmail(__("Welcome") . " " . $loginRadiusUsername . "!", $loginRadiusMessage, $socialloginProfileData['Email'], $loginRadiusUsername);
                }
                // new user notification to admin
                if ($blockObject->notifyAdmin() == "1") {
                    $loginRadiusAdminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
                    $loginRadiusAdminName = Mage::getStoreConfig('trans_email/ident_general/name');
                    $loginRadiusMessage = trim($blockObject->notifyAdminText());
                    if ($loginRadiusMessage == "") {
                        $loginRadiusMessage = __("New customer has been registered to your store with following details");
                    }
                    $loginRadiusMessage .= "<br/>" . __("Name") . " : " . $loginRadiusUsername . "<br/>" . __("Email") . " : " . $socialloginProfileData['Email'];
                    Mage::helper('sociallogin/loginhelper')->loginRadiusEmail(__("New User Registration"), $loginRadiusMessage, $loginRadiusAdminEmail, $loginRadiusAdminName);
                }
            }
            //login and redirect user
            $this->socialLoginUserLogin($customer->getId(), $fields['sociallogin_id'], $redirectionTo);
        }
        if ($verify) {
            $loginRadiusUsername = $socialloginProfileData['FirstName'] . " " . $socialloginProfileData['LastName'];
            Mage::helper('sociallogin/loginhelper')->verifyUser($socialloginProfileData['lrId'], $customer->getId(), $socialloginProfileData['Thumbnail'], $socialloginProfileData['Provider'], $socialloginProfileData['Email'], true, $loginRadiusUsername);
        }
    }

    /**
     * function responsible for inserting or updating data in loginradius tables
     *
     * @param  string $lrTable      table name
     * @param  array  $lrInsertData array of data to be inserted
     * @param bool    $update
     * @param string  $value        result of insertion
     *
     * @return mixed
     */
    public function SocialLoginInsert($lrTable, $lrInsertData, $update = false, $value = '')
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $sociallogin = $this->getMazeTable($lrTable);
        if (!$update) {
            try {
                $connection->insert($sociallogin, $lrInsertData);
            } catch (Exception $e) {
                Mage::logException($e);

            }

        } else {
            try {
                // update query magento way
                $connection->update(
                    $sociallogin, $lrInsertData, $value
                );
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        $connection->commit();
    }

    /**
     * Create table name!
     *
     * @param $tbl
     *
     * @return mixed
     */
    public function getMazeTable($tbl)
    {
        $tableName = Mage::getSingleton('core/resource')->getTableName($tbl);

        return ($tableName);
    }

    /**
     * Send email to specified to specified email address $to.
     *
     * @param string $subject
     * @param string $message
     * @param string $to
     * @param string $toName
     */
    public function loginRadiusEmail($subject, $message, $to, $toName)
    {
        $storeName = Mage::app()->getStore()->getGroup()->getName();
        $mail = new Zend_Mail('UTF-8'); //class for mail
        $mail->setBodyHtml($message); //for sending message containing html code
        $mail->setFrom("Owner", $storeName);
        $mail->addTo($to, $toName);
        $mail->setSubject($subject);
        try {
            $mail->send();
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    /**
     * function responsible for providing login to customer
     *
     * @param        $entityId          customer entity id
     * @param        $socialId          social id
     * @param string $loginOrRegister   is logging in after registration!
     */
    public function socialLoginUserLogin($entityId, $socialId, $loginOrRegister = 'Login')
    {
        $blockObj = new Loginradius_Sociallogin_Block_Sociallogin();
        $session = Mage::getSingleton("customer/session");
        $session->setloginRadiusId($socialId);
        $customer = Mage::getModel('customer/customer')->load($entityId);
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $session->setCustomerAsLoggedIn($customer);
        $functionForRedirectOption = 'get' . $loginOrRegister . 'RedirectOption';
        $Hover = $blockObj->$functionForRedirectOption();
        $functionForCustomRedirectOption = 'getCustom' . $loginOrRegister . 'RedirectOption';
        $write_url = $blockObj->$functionForCustomRedirectOption();
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        // check if logged in from callback page
        if (isset($_GET['loginradiuscheckout'])) {
            $currentUrl = Mage::helper('checkout/url')->getCheckoutUrl();
            Mage::app()->getResponse()->setRedirect($currentUrl)->sendResponse();
        }
        if ($Hover == 'account') {
            $currentUrl = $url . 'customer/account';
        } elseif ($Hover == 'index') {
            $currentUrl = $url;
        } elseif ($Hover == 'custom' && $write_url != '') {
            $currentUrl = $write_url;
        } elseif ($Hover == 'same') {
            $currentUrl = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
        } else {
            if (isset($_GET['redirect_to'])) {
                $currentUrl = trim($_GET['redirect_to']);
            } else {
                $currentUrl = $url;
            }

        }
        Mage::app()->getResponse()->setRedirect($currentUrl)->sendResponse();
    }

    /**
     * function handles customer email verification and displays appropriate message
     *
     * @param $slId
     * @param $entityId
     * @param $avatar
     * @param $provider
     * @param $email
     * @param $sendAdminEmail
     * @param $loginRadiusUsername
     */
    public function verifyUser($slId, $entityId, $avatar, $provider, $email, $sendAdminEmail = false, $loginRadiusUsername = '')
    {

        $this->blockObj = new Loginradius_Sociallogin_Block_Sociallogin();
        $vKey = md5(uniqid(rand(), true));
        $data = array();
        $data['sociallogin_id'] = $slId;
        $data['entity_id'] = $entityId;
        $data['avatar'] = $avatar;
        $data['verified'] = "0";
        $data['vkey'] = $vKey;
        $data['provider'] = $provider;
        // insert details in sociallogin table
        $this->SocialLoginInsert("sociallogin", $data);
        // send verification mail
        $message = __(Mage::helper('core')->htmlEscape(trim($this->blockObj->verificationText())));
        if ($message == "") {
            $message = __("Please click on the following link or paste it in browser to verify your email");
        }
        $message .= "<br/>" . Mage::getBaseUrl() . "sociallogin/?loginRadiusKey=" . $vKey;
        Mage::helper('sociallogin/loginhelper')->loginRadiusEmail(__("Email verification"), $message, $email, $email);

        if ($sendAdminEmail) {
            $loginRadiusAdminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
            $loginRadiusAdminName = Mage::getStoreConfig('trans_email/ident_general/name');
            $loginRadiusMessage = trim($this->blockObj->notifyAdminText());
            if ($loginRadiusMessage == "") {
                $loginRadiusMessage = __("New customer has been registered to your store with following details");
            }
            $loginRadiusMessage .= "<br/>" . __("Name") . " : " . $loginRadiusUsername . "<br/>" . __("Email") . " : " . $email;
            Mage::helper('sociallogin/loginhelper')->loginRadiusEmail(__("New User Registration"), $loginRadiusMessage, $loginRadiusAdminEmail, $loginRadiusAdminName);

        }
        $session = Mage::getSingleton('customer/session');
        $session->addSuccess(__('Confirmation link has been sent to your email address. Please verify your email by clicking on confirmation link.'));
        header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
        exit();
    }

    /**
     * Filter user profile data from loginradius api and return userprofile array
     *
     * @param object $userObject
     *
     * @return array
     */
    public function socialLoginFilterData($userObject)
    {
        //My code ends
        $profileDataFiltered = array();
        $profileDataFiltered['Email'] = isset($userObject->Email[0]->Value) ? $userObject->Email[0]->Value : '';
        $profileDataFiltered['Provider'] = empty($userObject->Provider) ? "" : $userObject->Provider;
        $profileDataFiltered['FirstName'] = empty($userObject->FirstName) ? "" : $userObject->FirstName;
        $profileDataFiltered['FullName'] = empty($userObject->FullName) ? "" : $userObject->FullName;
        $profileDataFiltered['NickName'] = empty($userObject->NickName) ? "" : $userObject->NickName;
        $profileDataFiltered['LastName'] = empty($userObject->LastName) ? "" : $userObject->LastName;
        if (isset($userObject->Addresses) && is_array($userObject->Addresses)) {
            foreach ($userObject->Addresses as $address) {
                if (isset($address->Address1) && !empty($address->Address1)) {
                    $profileDataFiltered['Address'] = $address->Address1;
                    break;
                }
            }
        } elseif (isset($userObject->Addresses) && is_string($userObject->Addresses)) {
            $profileDataFiltered['Address'] = isset($userObject->Addresses) && $userObject->Addresses != "" ? $userObject->Addresses : "";
        } else {
            $profileDataFiltered['Address'] = "";
        }
        $profileDataFiltered['PhoneNumber'] = empty($userObject->PhoneNumbers['0']->PhoneNumber) ? "" : $userObject->PhoneNumbers['0']->PhoneNumber;
        $profileDataFiltered['State'] = empty($userObject->State) ? "" : $userObject->State;
        $profileDataFiltered['City'] = empty($userObject->City) || $userObject->City == "unknown" ? "" : $userObject->City;
        $profileDataFiltered['Industry'] = empty($userObject->Positions['0']->Comapany->Name) ? "" : $userObject->Positions['0']->Comapany->Name;
        if (isset($userObject->Country->Code) && is_string($userObject->Country->Code)) {
            $profileDataFiltered['Country'] = $userObject->Country->Code;
        } else {
            $profileDataFiltered['Country'] = "";
        }
        $profileDataFiltered['Thumbnail'] = $this->socialLoginFilterAvatar($userObject->ID, $userObject->ThumbnailImageUrl, $profileDataFiltered['Provider']);


        if (empty($profileDataFiltered['FirstName'])) {
            if (!empty($profileDataFiltered['FullName'])) {
                $profileDataFiltered['FirstName'] = $profileDataFiltered['FullName'];
            } elseif (!empty($profileDataFiltered['ProfileName'])) {
                $profileDataFiltered['FirstName'] = $profileDataFiltered['ProfileName'];
            } elseif (!empty($profileDataFiltered['NickName'])) {
                $profileDataFiltered['FirstName'] = $profileDataFiltered['NickName'];
            } elseif (!empty($email)) {
                $user_name = explode('@', $email);
                $profileDataFiltered['FirstName'] = str_replace("_", " ", $user_name[0]);
            } else {
                $profileDataFiltered['FirstName'] = $userObject->ID;
            }
        }

        $profileDataFiltered['Gender'] = (!empty($userObject->Gender) ? $userObject->Gender : '');
        if (strtolower(substr($profileDataFiltered['Gender'], 0, 1)) == 'm') {
            $profileDataFiltered['Gender'] = '1';
        } elseif (strtolower(substr($profileDataFiltered['Gender'], 0, 1)) == 'f') {
            $profileDataFiltered['Gender'] = '2';
        } else {
            $profileDataFiltered['Gender'] = '';
        }
        $profileDataFiltered['BirthDate'] = (!empty($userObject->BirthDate) ? $userObject->BirthDate : '');
        if ($profileDataFiltered['BirthDate'] != "") {
            switch ($profileDataFiltered['Provider']) {
                case 'facebook':
                case 'foursquare':
                case 'yahoo':
                case 'openid':
                    break;

                case 'google':
                    $temp = explode('/', $profileDataFiltered['BirthDate']); // yy/mm/dd
                    $profileDataFiltered['BirthDate'] = $temp[1] . "/" . $temp[2] . "/" . $temp[0];
                    break;

                case 'twitter':
                case 'linkedin':
                case 'vkontakte':
                case 'live';
                    $temp = explode('/', $profileDataFiltered['BirthDate']); // dd/mm/yy
                    $profileDataFiltered['BirthDate'] = $temp[1] . "/" . $temp[0] . "/" . $temp[2];
                    break;
            }
        }

        return $profileDataFiltered;
    }

    /**
     * Get thumbnail image url
     *
     * @param $id
     * @param $imgUrl
     * @param $provider
     *
     * @return string
     */
    public function socialLoginFilterAvatar($id, $imgUrl, $provider)
    {
        $thumbnail = (!empty($imgUrl) ? trim($imgUrl) : '');
        if (empty($thumbnail) && ($provider == 'facebook')) {
            $thumbnail = "http://graph.facebook.com/" . $id . "/picture?type=large";
        }

        return $thumbnail;
    }

    /**
     * check if url is valid or not.
     *
     * @param string $url
     *
     * @return bool true/false
     */
    public function validateUrl($url)
    {
        $validUrlExpression = "/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|)?[a-z0-9_\-]+[a-z0-9_\-\.]+\.[a-z]{2,4}(\/+[a-z0-9_\.\-\/]*)?$/i";

        return (bool)preg_match($validUrlExpression, $url);
    }

    /**
     * Generate random email
     *
     * @param $userObject
     *
     * @return string
     */
    public function getAutoGeneratedEmail($userObject)
    {
        $emailName = str_replace(array("/", "."), "_", substr($userObject->ID, -10, -1));
        $email = $emailName . '@' . $userObject->Provider . '.com';
        $userId = Mage::helper('sociallogin/loginhelper')->loginRadiusRead("customer_entity", "email exists pop1", array($email), true);
        if ($rowArray = $userId->fetch()) {
            $emailName = str_replace(array("/", "."), "_", substr($userObject->ID, -10));
            $emailName = str_shuffle($emailName);
            $email = $emailName . '@' . $userObject->Provider . '.com';
        }

        return $email;
    }

    /**
     * Read data from LoginRadius related tables
     *
     * @param      $table
     * @param      $handle
     * @param      $params
     * @param bool $result
     *
     * @return bool|Zend_Db_Statement_Interface
     */
    public function loginRadiusRead($table, $handle, $params, $result = false)
    {
        $socialLoginConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $Tbl = $this->getMazeTable($table);
        $customerTable = $this->getMazeTable('customer_entity');
        $websiteId = Mage::app()->getWebsite()->getId();
        $storeId = Mage::app()->getStore()->getId();
        $query = "";
        switch ($handle) {
            case "email exists pop1":
                $query = "select entity_id from $Tbl where email = '" . $params[0] . "' and website_id = $websiteId and store_id = $storeId";
                break;
            case "get_user_by_social_id":
                $query = "SELECT $Tbl.entity_id, verified from $Tbl JOIN $customerTable ON $customerTable.entity_id = $Tbl.entity_id WHERE $Tbl.sociallogin_id = '" . $params[0] . "' AND $customerTable.website_id = $websiteId AND $customerTable.store_id =" . $storeId;
                break;
            case "get_user_from_customer_entity":
                $query = "select entity_id from $Tbl where entity_id = " . $params[0] . " and website_id = $websiteId and store_id = $storeId";
                break;
            case "email_already_exists":
                $query = "select * from $Tbl where email = '" . $params[0] . "' and website_id = $websiteId and store_id = $storeId";
                break;
            case "email exists sl":
                $query = "select verified,sociallogin_id from $Tbl where entity_id = '" . $params[0] . "' and provider = '" . $params[1] . "'";
                break;
            case "provider exists in sociallogin":
                $query = "select entity_id from $Tbl where entity_id = '" . $params[0] . "' and provider = '" . $params[1] . "'";
                break;
            case "verification":
                $query = "select entity_id, provider from $Tbl where vkey = '" . $params[0] . "'";
                break;
            case "verification2":
                $query = "select entity_id from $Tbl where entity_id = " . $params[0] . " and provider = '" . $params[1] . "' and vkey != '' ";
                break;
        }
        $select = $socialLoginConn->query($query);
        if ($result) {
            return $select;
        }
        if ($select->fetch()) {
            return true;
        }

        return false;
    }

    /**
     * Function to perform social linking
     *
     * @param      $entityId  logged in customer entity id
     * @param      $socialId  social id
     * @param      $provider  provider
     * @param      $thumbnail thumbnail image url
     * @param bool $unique
     */
    public function loginRadiusSocialLinking($entityId, $socialId, $provider, $thumbnail, $unique = false)
    {
        $session = Mage::getSingleton('customer/session');
        // check if any account from this provider is already linked
        if (Mage::helper('sociallogin/loginhelper')->isAlreadyLoggedIn()) {
            if (Mage::helper('sociallogin/loginhelper')->loginRadiusRead("sociallogin", "get_user_by_social_id", array($socialId))) {
                $session->addError(__('This accounts is already linked with an account.'));
                header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "customer/account");
                exit();
            } elseif ($unique && Mage::helper('sociallogin/loginhelper')->loginRadiusRead("sociallogin", "provider exists in sociallogin", array($entityId, $provider))) {
                $session->addError(__('Multiple accounts cannot be linked from the same Social ID Provider.'));
                header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "customer/account");
                exit();
            }
        }
        $socialLoginLinkData = array();
        $socialLoginLinkData['sociallogin_id'] = $socialId;
        $socialLoginLinkData['entity_id'] = $entityId;
        $socialLoginLinkData['provider'] = empty($provider) ? "" : $provider;
        $socialLoginLinkData['avatar'] = Mage::helper('sociallogin/loginHelper')->socialLoginFilterAvatar($socialId, $thumbnail, $provider);
        $socialLoginLinkData['avatar'] = ($socialLoginLinkData['avatar'] == "") ? null : $socialLoginLinkData['avatar'];
        Mage::helper('sociallogin/loginhelper')->SocialLoginInsert("sociallogin", $socialLoginLinkData);
        if (Mage::helper('sociallogin/loginhelper')->isAlreadyLoggedIn()) {
            $session->addSuccess(__('Account linked successfully.'));
            header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "customer/account");
            exit();
        }
    }

    /**
     * Check if user is already loggedin
     *
     * @return bool
     */
    public function isAlreadyLoggedIn()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return true;
        }

        return false;
    }

    /**
     * Set social network profile data in session
     *
     * @param $id
     * @param $socialloginProfileData
     */
    public function setInSession($id, $socialloginProfileData)
    {
        $socialloginProfileData['lrId'] = $id;
        Mage::getSingleton('core/session')->setSocialLoginData($socialloginProfileData);
    }

}
