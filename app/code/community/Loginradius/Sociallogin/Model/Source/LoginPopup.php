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
 *  sociallogin loginpopup source model
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_Model_Source_Loginpopup which return loginpopup configuration options in admin
 */
class Loginradius_Sociallogin_Model_Source_Loginpopup
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => '1', 'label' => __('Yes') . '<br/>');
        $result[] = array('value' => '0', 'label' => __('No') . '<br/>');

        return $result;
    }
}