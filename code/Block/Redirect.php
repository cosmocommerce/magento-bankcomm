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
   
        $fields=$standard->setOrder($this->getOrder())->getStandardCheckoutFormFields();    
       

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Bankcomm in a few seconds.');
        $html.= '<form action="'.$standard->getBankcommUrl().'" id="bankcomm_payment_checkout" name="bankcomm_payment_checkout" method="POST">';
        
         $html.= '<input type = "hidden" name = "interfaceVersion" value = "'.$fields['interfaceVersion'].'">';
         $html.= '<input type = "hidden" name = "merID" value = "'.$fields['merchID'].'">';
         $html.= '<input type = "hidden" name = "orderid" value = "'.$fields['orderid'].'">';
         $html.= '<input type = "hidden" name = "orderDate" value = "'.$fields['orderDate'].'">';
         $html.= '<input type = "hidden" name = "orderTime" value = "'.$fields['orderTime'].'">';
         $html.= '<input type = "hidden" name = "tranType" value = "'.$fields['tranType'].'">';
         $html.= '<input type = "hidden" name = "amount" value = "'.$fields['amount'].'">';
         $html.= '<input type = "hidden" name = "curType" value = "'.$fields['curType'].'">';
         $html.= '<input type = "hidden" name = "orderContent" value = "'.$fields['orderContent'].'">';
         $html.= '<input type = "hidden" name = "orderMono" value = "'.$fields['orderMono'].'">';
         $html.= '<input type = "hidden" name = "phdFlag" value = "'.$fields['phdFlag'].'">';
         $html.= '<input type = "hidden" name = "notifyType" value = "'.$fields['notifyType'].'">';
         $html.= '<input type = "hidden" name = "merURL" value = "'.$fields['merURL'].'">';
         $html.= '<input type = "hidden" name = "goodsURL" value = "'.$fields['goodsURL'].'">';
         $html.= '<input type = "hidden" name = "jumpSeconds" value = "'.$fields['jumpSeconds'].'">';
         $html.= '<input type = "hidden" name = "payBatchNo" value = "'.$fields['payBatchNo'].'">';
         $html.= '<input type = "hidden" name = "proxyMerName" value = "'.$fields['proxyMerName'].'">';
         $html.= '<input type = "hidden" name = "proxyMerType" value = "'.$fields['proxyMerType'].'">';
         $html.= '<input type = "hidden" name = "proxyMerCredentials" value = "'.$fields['proxyMerCredentials'].'">';
         $html.= '<input type = "hidden" name = "netType" value = "'.$fields['netType'].'">';
         $html.= '<input type = "hidden" name = "merSignMsg" value = "'.$fields['signMsg_value'].'">';
         $html.= '<input type = "hidden" name = "issBankNo" value = "'.$fields['issBankNo'].'">';
        
        
        //$html.= $formHTML;
        $html.= '</form>';
        //$html.="<script type="text/javascript">window.open('http://www.baidu.com', 'window name', 'window settings');</script>";
        $html.= '<script type="text/javascript">document.getElementById("bankcomm_payment_checkout").submit();</script>';
        $html.= '</body></html>';


        return $html;
    }
}