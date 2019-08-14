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
 *  sociallogin sociallogin block
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

// Define LoginRadius domain
if (!defined('LR_DOMAIN')) {
    define('LR_DOMAIN', 'api.loginradius.com');
}

/**
 * Class Loginradius_Sociallogin_Block_Sociallogin bloch which contains function to get all the configuration settings for loginradius extension
 */
class Loginradius_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template
{

    /**
     * Constructor for class Loginradius_Sociallogin_Block_Sociallogin
     */
    public function _construct()
    {
        parent::_construct();
        if ($this->horizontalShareEnable() == "1" || $this->verticalShareEnable() == "1") {
            $this->setTemplate('sociallogin/socialshare.phtml');
        }
    }

    /**
     * @return mixed 1/0 according to option enabled or disabled
     */
    public function horizontalShareEnable()
    {
        return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalShareEnable');
    }

    /**
     * @return mixed 1/0 according to option enabled or disabled
     */
    public function verticalShareEnable()
    {
        return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalShareEnable');
    }

    /**
     * Override _prepareLayout method
     *
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return mixed data from sociallogin table
     */

    public function getSociallogin()
    {
        if (!$this->hasData('sociallogin')) {
            $this->setData('sociallogin', Mage::registry('sociallogin'));
        }

        return $this->getData('sociallogin');
    }

    /**
     * Check if debugging is on or off
     *
     * @return mixed
     */
    public function isDebuggingOn()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/debugMode');
    }

    /**
     * Get LoginRadius API Key
     */
    public function getApikey()
    {
        return trim(Mage::getStoreConfig('sociallogin_options/apiSettings/apikey'));
    }

    /**
     * Get LoginRadius API Secret
     */
    public function getApiSecret()
    {
        return trim(Mage::getStoreConfig('sociallogin_options/apiSettings/apisecret'));
    }

    /**
     * Get thumbnail image to be displayed as avatar or false if not exists
     *
     * @param $id
     *
     * @return mix
     */
    public function getAvatar($id)
    {
        $socialLoginConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $SocialLoginTbl = Mage::getSingleton('core/resource')->getTableName("sociallogin");
        $select = $socialLoginConn->query("select avatar from $SocialLoginTbl where entity_id = '$id' limit 1");
        if ($rowArray = $select->fetch()) {
            if (($avatar = trim($rowArray['avatar'])) != "") {
                return $avatar;
            }
        }

        return false;
    }

    /**
     * Get script and css for email required popup
     *
     * @return string
     */
    public function getPopupScriptUrl()
    {
        $jsPath = Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/js/popup.js', array('_area' => 'frontend'));
        $cssPath = Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/css/popup.css', array('_area' => 'frontend'));

        return '<script  type="text/javascript" src="' . $jsPath . '"></script><link rel = "stylesheet" href="' . $cssPath . '" media = "all" />';

    }

    /**
     * configuration to display social login in side bar
     *
     * @return mixed
     */
    public function getShowDefault()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/showdefault');
    }

    /**
     * configuration to display social login above login form
     *
     * @return mixed
     */
    public function getAboveLogin()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/aboveLogin');
    }

    /**
     * configuration to display social login below login form
     *
     * @return mixed
     */
    public function getBelowLogin()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/belowLogin');
    }

    /**
     * configuration to display social login above registration form
     *
     * @return mixed
     */
    public function getAboveRegister()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/aboveRegister');
    }

    /**
     * configuration to display social login below registration form
     *
     * @return mixed
     */
    public function getBelowRegister()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/belowRegister');
    }

    /**
     * Get icon size as configured in extension configuration page
     */
    public function iconSize()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/iconSize');
    }

    /**
     * get no. of icons per row in social login interface
     *
     * @return int
     */
    public function iconsPerRow()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/iconsPerRow');
    }

    /**
     * Get background color for social login interface
     *
     * @return mixed
     */
    public function backgroundColor()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/backgroundColor');
    }

    /**
     * Get redirection url after login
     *
     * @return mixed
     */
    public function getLoginRedirectOption()
    {
        return Mage::getStoreConfig('sociallogin_options/basicSettings/redirectAfterLogin');
    }

    /**
     * Get redirection url after registration
     *
     * @return mixed
     */
    public function getRegistrationRedirectOption()
    {
        return Mage::getStoreConfig('sociallogin_options/basicSettings/redirectAfterRegistration');
    }

    /**
     * Get redirection custom url after login
     *
     * @return mixed
     */
    public function getCustomLoginRedirectOption()
    {
        return Mage::getStoreConfig('sociallogin_options/basicSettings/customUrlLogin');
    }

    /**
     * Get redirection custom url after registration
     *
     * @return mixed
     */
    public function getCustomRegistrationRedirectOption()
    {
        return Mage::getStoreConfig('sociallogin_options/basicSettings/customUrlRegistration');
    }

    /**
     * Check if email is required if social network does not provide email.
     *
     * @return mixed 1/0
     */
    public function getEmailRequired()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/emailrequired');
    }

    public function verificationText()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/verificationText');
    }

    /**
     * Get email verification text to be send to customer
     *
     * @return mixed
     */
    public function getPopupText()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/popupText');
    }

    /**
     * Get popup error message which to be shown while there is error in email syntax..
     *
     * @return mixed
     */
    public function getPopupError()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/popupError');
    }

    /**
     * Check if username password should be emaild to customer
     *
     * @return mixed 1/0
     */
    public function notifyUser()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/notifyUser');
    }

    /**
     * Text to be send with Username and password to registerd customer
     *
     * @return mixed
     */
    public function notifyUserText()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/notifyUserText');
    }

    /**
     * Check if need to send email to admin on registration.
     *
     * @return mixed
     */
    public function notifyAdmin()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/notifyAdmin');
    }

    /**
     * Text to be send with to magento site admin
     *
     * @return mixed
     */
    public function notifyAdminText()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/notifyAdminText');
    }

    /**
     * Check if horizontal sharing is enabled on products page or not
     *
     * @return mixed 1/0
     */
    public function horizontalShareProduct()
    {
        return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalShareProduct');
    }

    /**
     * Check if vertical sharing is enabled on products page or not
     *
     * @return mixed 1/0
     */
    public function verticalShareProduct()
    {
        return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalShareProduct');
    }

    /**
     * Check if horizontal sharing is enabled on checkout success page or not
     *
     * @return mixed 1/0
     */
    public function horizontalShareSuccess()
    {
        return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalShareSuccess');
    }

    /**
     * Check if vertical sharing is enabled on checkout success page or not
     *
     * @return mixed 1/0
     */
    public function verticalShareSuccess()
    {
        return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalShareSuccess');
    }

    /**
     * Get name of the horizontal sharing theme
     *
     * @return string
     */
    public function horizontalSharingTheme()
    {
        return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalSharingTheme');
    }

    /**
     * Get name of the horizontal sharing theme
     *
     * @return string
     */
    public function verticalSharingTheme()
    {
        return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalSharingTheme');
    }

    /**
     * Get vertical sharing alignment
     */
    public function verticalAlignment()
    {
        return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalAlignment');
    }

    /**
     * Get array of horizontal sharing providers selected
     *
     * @return array
     */
    public function horizontalSharingProviders()
    {
        return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalSharingProvidersHidden');
    }

    /**
     * Get array of vertical sharing providers selected
     *
     * @return array
     */
    public function verticalSharingProviders()
    {
        return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalSharingProvidersHidden');
    }

    /**
     * Get array of horizontal counter providers selected
     *
     * @return array
     */
    public function horizontalCounterProviders()
    {
        return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalCounterProvidersHidden');
    }

    /**
     * Get array of vertical counter providers selected
     *
     * @return array
     */
    public function verticalCounterProviders()
    {
        return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalCounterProvidersHidden');
    }

    /**
     * check if profile fields are required or not
     *
     * @return mixed 1/0
     */
    public function getProfileFieldsRequired()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/profileFieldsRequired');
    }

    /**
     * Check if profile should be updated each time on customer login.
     *
     * @return mixed 1/0
     */
    public function updateProfileData()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/updateProfileData');
    }

    /**
     * Check if linking option is enabled or not!
     */
    public function getLinking()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/socialLinking');
    }

    /**
     * Container for Social Login Interface
     */
    public function getSocialLoginContainer()
    {
        $lrKeys = $this->getApiKeys();
        $AdvancedSettings = $this->getAdvancedSettings();
        $lrSettings = array_merge($lrKeys, $AdvancedSettings);
        $UserAuth = $this->getApiValidation(trim($lrSettings['apikey']), trim($lrSettings['apisecret']));
        echo '<div class="block" style="margin-top:15px"><div class="block-title"><strong><span>' . $this->getSocialLoginTitle() . '</span></strong></div><div class="block-content"><p class="empty">';
        if ($lrKeys['apikey'] == "" && $lrKeys['apikey'] == "apisecret") {
            echo '<p style ="color:red;">' . $this->__('To activate your plugin, please log in to LoginRadius and get API Key & Secret. Web') . ': <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b></p>';
        } elseif ($UserAuth == false) {
            echo '<p style ="color:red;">' . $this->__('Your LoginRadius API Key and Secret is not valid, please correct it or contact LoginRadius support at') . ' <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b></p>';
        } else {
            echo '<div style="margin:5px"><div style="margin-bottom:5px">' . trim($this->getLoginRadiusTitle()) . '</div><div class="interfacecontainerdiv"></div></div>';
        }
        echo '</p></div></div>';
    }

    /**
     * Get LoginRadius Api Key and Secret Key
     *
     * @return mixed
     */
    public function getApiKeys()
    {
        return Mage::getStoreConfig('sociallogin_options/apiSettings');
    }

    /**
     * Get Advanced Settings for extension
     *
     * @return mixed
     */
    public function getAdvancedSettings()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings');
    }

    /**
     * Validate LoginRadius keys!
     */
    public function getApiValidation($ApiKey, $ApiSecrete)
    {
        if (!empty($ApiKey) && !empty($ApiSecrete) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $ApiKey) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $ApiSecrete)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get title for social login interface
     */
    public function getSocialLoginTitle()
    {
        return Mage::getStoreConfig('sociallogin_options/advancedSettings/loginradius_title');
    }

}