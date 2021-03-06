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
 * @category    CosmoCommerce
 * @package     CosmoCommerce_Bankcomm
 * @copyright   Copyright (c) 2009-2013 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @contact :
 * T: +86-021-66346672
 * L: Shanghai,China
 * M:sales@cosmocommerce.com
 */

class CosmoCommerce_Bankcomm_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'EN', 'label' => Mage::helper('bankcomm')->__('English')),
            array('value' => 'FR', 'label' => Mage::helper('bankcomm')->__('French')),
            array('value' => 'DE', 'label' => Mage::helper('bankcomm')->__('German')),
            array('value' => 'IT', 'label' => Mage::helper('bankcomm')->__('Italian')),
            array('value' => 'ES', 'label' => Mage::helper('bankcomm')->__('Spain')),
            array('value' => 'NL', 'label' => Mage::helper('bankcomm')->__('Dutch')),
        );
    }
}



