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
 *  sociallogin observer model
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_Model_Observer responsible for LoginRadius api keys verification!
 */
class Loginradius_Sociallogin_Model_Observer extends Mage_Core_Helper_Abstract
{
    /**
     * @throws Exception while api keys are not valid!
     */
    public function validateLoginradiusKeys()
    {
        $post = Mage::app()->getRequest()->getPost();

        //$post['groups']['verticalSharing']['fields']['verticalCounterProvidersHidden'] = str_replace('  ', '', $post['groups']['verticalSharing']['fields']['verticalCounterProvidersHidden']);
        //$post['groups']['horizontalSharing']['fields']['horizontalCounterProvidersHidden'] = str_replace('  ', '', $post['groups']['horizontalSharing']['fields']['horizontalCounterProvidersHidden']);
        if (isset($post['config_state']['sociallogin_options_apiSettings'])) {
            $apiKey = $post['groups']['apiSettings']['fields']['apikey']['value'];
            $apiSecret = $post['groups']['apiSettings']['fields']['apisecret']['value'];
            $validateUrl = 'https://api.loginradius.com/api/v2/app/validate?apikey=' . rawurlencode($apiKey) . '&apisecret=' . rawurlencode($apiSecret);
            $result = $this->getLoginRadiusKeysValidationStatus($validateUrl);
            if (isset($result['status']) && $result['status']) {
                $result['message'] = '';
                $result['status'] = 'Success';
            } else {
                if ($result['message'] == 'API_KEY_NOT_FORMATED') {
                    $result['message'] = 'LoginRadius API key is not correct.';
                } elseif ($result['message'] == 'API_SECRET_NOT_FORMATED') {
                    $result['message'] = 'LoginRadius API Secret key is not correct.';
                } elseif ($result['message'] == 'API_KEY_NOT_VALID') {
                    $result['message'] = 'LoginRadius API key is not valid.';
                } elseif ($result['message'] == 'API_SECRET_NOT_VALID') {
                    $result['message'] = 'LoginRadius API Secret key is not valid.';
                }
            }
            if ($result['status'] != 'Success') {
                throw new Exception($result['message']);
            }
        }
    }

    /**
     * function is used to get response form LoginRadius api validation.
     *
     * @param string $url
     *
     * @return array $result
     */
    public function getLoginRadiusKeysValidationStatus($url)
    {
        $loginradiusObject = Mage::helper('sociallogin/loginradiussdk');
        $response = json_decode($loginradiusObject->accessLoginradiusApi($url));
        $result['status'] = isset($response->Status) ? $response->Status : false;
        $result['message'] = isset($response->Messages[0]) ? $response->Messages[0] : 'an error occurred';

        return $result;
    }
}
