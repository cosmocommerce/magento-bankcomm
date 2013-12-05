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
 * @category	CosmoCommerce
 * @package 	CosmoCommerce_Bankcomm
 * @copyright	Copyright (c) 2009-2014 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @contact :
 * T: +86-021-66346672
 * L: Shanghai,China
 * M:sales@cosmocommerce.com
 */
class CosmoCommerce_Bankcomm_Block_Redirect extends Mage_Core_Block_Abstract
{

	protected function _toHtml()
	{
		$standard = Mage::getModel('bankcomm/payment');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getBankcommUrl())
            ->setId('bankcomm_payment_checkout')
            ->setName('bankcomm_payment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $formHTML = $form->toHtml();

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Bankcomm in a few seconds.');
        $html.= $formHTML;
        //$html.="<script type="text/javascript">window.open('http://www.baidu.com', 'window name', 'window settings');</script>";
        $html.= '<script type="text/javascript">document.getElementById("bankcomm_payment_checkout").submit();</script>';
        $html.= '</body></html>';


        return $html;
    }
}