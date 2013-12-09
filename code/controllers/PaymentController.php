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
 * @copyright   Copyright (c) 2009-2014 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @contact :
 * T: +86-021-66346672
 * L: Shanghai,China
 * M:sales@cosmocommerce.com
 */
class CosmoCommerce_Bankcomm_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function logTrans($trans,$type){
		$log = Mage::getModel('bankcomm/log');
        $log->setLogAt(time());
        $log->setOrderId(implode('|',$trans));
        $log->setTradeNo(null);
        $log->setType($type);
        $log->setPostData(implode('|',$trans));
        $log->save();
    }
    public function getOrder()
    {
        if ($this->_order == null)
        {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            if($orderId=$session->getBankcommPaymentOrderId()){
            
                $order = Mage::getModel('sales/order')->load($orderId);
                if (!$order->getId())
                {
                    $this->norouteAction();
                    return;
                }
                $order_cid=$order->getCustomerId();
                $current_cid=0;
                if(Mage::helper('customer')->getCustomer()){
                    $current_cid=Mage::helper('customer')->getCustomer()->getId();
                }else{
                    $this->_redirect('customer/account/login');
                    return;
                }
                
                if ($current_cid!=$order_cid)
                {
                    $this->norouteAction();
                    return;
                }
            
                $this->_order->load($orderId);
            }else{
                $this->_order->loadByIncrementId($session->getLastRealOrderId());
            }
        }
        return $this->_order;
    }

    /**
     * When a customer chooses Bankcomm on Checkout/Payment page
     *
     */
     
    public function payAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $orderId = (int) $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $session->setBankcommPaymentOrderId($orderId);
        }
        $order = $this->getOrder();
        if (!$order)
        {
            return;
        }
        if (!$order->getId())
        {
            return;
        }

        $order->addStatusToHistory(
        $order->getStatus(),
        Mage::helper('bankcomm')->__('Customer was redirected to payment confirm page')
        );
        $order->save();

        
        $this->loadLayout();
        $this->renderLayout();
    }

    
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $order = $this->getOrder();

        if (!$order->getId())
        {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
        $order->getStatus(),
        Mage::helper('bankcomm')->__('Customer was redirected to Bankcomm')
        );
        $order->save();

        $this->getResponse()
        ->setBody($this->getLayout()
        ->createBlock('bankcomm/redirect')
        ->setOrder($order)
        ->toHtml());

        $session->unsQuoteId();
    }

    public function notifyAction()
    {
        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            $method = 'post';


        } else if ($this->getRequest()->isGet())
        {
            $postData = $this->getRequest()->getQuery();
            $method = 'get';

        } else
        {
            return;
        }
		$bankcomm = Mage::getModel('bankcomm/payment');
		
        $this->logTrans($postData,implode('|',$postData));//交易成功
		
    }

    protected function saveInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice())
        {
            $convertor = Mage::getModel('sales/convert_order');
            $invoice = $convertor->toInvoice($order);
            foreach ($order->getAllItems() as $orderItem)
            {
                if (!$orderItem->getQtyToInvoice())
                {
                    continue ;
                }
                $item = $convertor->itemToInvoiceItem($orderItem);
                $item->setQty($orderItem->getQtyToInvoice());
                $invoice->addItem($item);
            }
            $invoice->collectTotals();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();
            return true;
        }

        return false;
    }

    /**
     *  Success payment page
     *
     *  @param    none
     *  @return	  void
     */
    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getBankcommPaymentQuoteId());
        $session->unsBankcommPaymentQuoteId();

        $order = $this->getOrder();

        if (!$order->getId())
        {
            $this->norouteAction();
            return;
        }

        $order->addStatusToHistory(
        $order->getStatus(),
        Mage::helper('bankcomm')->__('Customer successfully returned from Bankcomm')
        );

        $order->save();

        $this->_redirect('checkout/onepage/success');
    }

    /**
     *  Failure payment page
     *
     *  @param    none
     *  @return	  void
     */
    public function errorAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $errorMsg = Mage::helper('bankcomm')->__(' There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (!$order->getId())
        {
            $this->norouteAction();
            return;
        }
        if ($order instanceof Mage_Sales_Model_Order && $order->getId())
        {
            $order->addStatusToHistory(
            Mage_Sales_Model_Order::STATE_CANCELED,//$order->getStatus(),
            Mage::helper('bankcomm')->__('Customer returned from Bankcomm.').$errorMsg
            );

            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }
	
	
    
}
