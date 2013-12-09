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
class CosmoCommerce_Bankcomm_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'bankcomm_payment';
    protected $_formBlockType = 'bankcomm/form';
	protected $_gateway="https://pay.95559.com.cn/netpay/MerPayB2C";

    
    
    
    // Bankcomm return codes of payment
    const RETURN_CODE_ACCEPTED      = 'Success';
    const RETURN_CODE_TEST_ACCEPTED = 'Success';
    const RETURN_CODE_ERROR         = 'Fail';

    // Payment configuration
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    // Order instance
    protected $_order = null;

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
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
    public function getBankcommUrl()
    {
		$bankcomm = Mage::getModel('bankcomm/payment');
		$sandbox=$bankcomm->getConfigData('sandbox');
		if($sandbox){
            $this->_gateway ="https://pbanktest.95559.com.cn/netpay/MerPayB2C";
        }
        return $this->_gateway;
    }

    /**
     *  Return back URL
     *
     *  @return	  string URL
     */
	protected function getReturnURL()
	{
		return Mage::getUrl('bankcomm/payment/notify/', array('_secure' => true));
	}

	/**
	 *  Return URL for Bankcomm success response
	 *
	 *  @return	  string URL
	 */
	protected function getSuccessURL()
	{
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
	}

    /**
     *  Return URL for Bankcomm failure response
     *
     *  @return	  string URL
     */
    protected function getErrorURL()
    {
        return Mage::getUrl('bankcomm/payment/error', array('_secure' => true));
    }

	/**
	 *  Return URL for Bankcomm notify response
	 *
	 *  @return	  string URL
	 */
	protected function getNotifyURL()
	{
		return Mage::getUrl('bankcomm/payment/notify/', array('_secure' => true));
	}

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('bankcomm/form_payment', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('bankcomm/payment/pay');
    }

    /**
     *  Return Standard Checkout Form Fields for request to Bankcomm
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields()
    {
        $session = Mage::getSingleton('checkout/session');
        
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        } 
        $logistics_fees=$order->getShippingAmount();
		$converted_final_price=$order->getGrandTotal()-$logistics_fees;
		
           
        $socket_ip = "127.0.0.1";
        $socket_port = "8080";
        $merchID = "301310063009501"; 
        $interfaceVersion = '1.0.0.0';		
        $merID = $merchID; //商户号为固定	
        $orderid = $order->getRealOrderId();
        $orderDate = date('Ymd',strtotime($order->getCreatedAt()));
        $orderTime = date('his',strtotime($order->getCreatedAt()));
        $tranType = '0';
        $amount =sprintf('%.2f', $converted_final_price);
        $curType = 'CNY';
        $orderContent = $order->getRealOrderId();
        $orderMono = $order->getRealOrderId();
        $phdFlag = '';
        $notifyType =1;
        $merURL = $this->getNotifyURL();
        $goodsURL = $this->getReturnURL();
        $jumpSeconds = '';
        $payBatchNo = '';
        $proxyMerName = '';
        $proxyMerType = '';
        $proxyMerCredentials = '';
        $netType = 0;
        $issBankNo = '';
        $tranCode = "cb2200_sign";

        $source = "";
        //htmlentities($orderMono,"ENT_QUOTES","GB2312");
        //连接字符串
        $source = $interfaceVersion."|".$merID."|".$orderid."|".$orderDate."|".$orderTime."|".$tranType."|"
        .$amount."|".$curType."|".$orderContent."|".$orderMono."|".$phdFlag."|".$notifyType."|".$merURL."|"
        .$goodsURL."|".$jumpSeconds."|".$payBatchNo."|".$proxyMerName."|".$proxyMerType."|".$proxyMerCredentials."|".$netType;


        //连接地址
        $socketUrl = "tcp://".$socket_ip.":".$socket_port;
        $fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
        $retMsg="";
        //
        if (!$fp) {
            $this->logTrans($errno,$errno);
        } else 
        {
            $in  = "<?xml version='1.0' encoding='UTF-8'?>";
            $in .= "<Message>";
            $in .= "<TranCode>".$tranCode."</TranCode>";
            $in .= "<MsgContent>".$source."</MsgContent>";
            $in .= "</Message>";
            fwrite($fp, $in);
            while (!feof($fp)) {
                $retMsg =$retMsg.fgets($fp, 1024);
                
            }
            fclose($fp);
        }	
        
        $dom = new DOMDocument;
        $dom->loadXML($retMsg);

        $retCode = $dom->getElementsByTagName('retCode');
        $retCode_value = $retCode->item(0)->nodeValue;

        $errMsg = $dom->getElementsByTagName('errMsg');
        $errMsg_value = $errMsg->item(0)->nodeValue;

        $signMsg = $dom->getElementsByTagName('signMsg');
        $signMsg_value = $signMsg->item(0)->nodeValue;

        $orderUrl = $dom->getElementsByTagName('orderUrl');
        $orderUrl_value = $orderUrl->item(0)->nodeValue;
        $this->logTrans( $retCode_value." ".$errMsg_value." ".$signMsg_value." ".$orderUrl_value,$retCode_value);

        if($retCode_value != "0"){
            $this->logTrans($errMsg_value,$retCode_value);
        }else{
        
           
         
        
        $parameter = array(
                           'interfaceVersion'        => $interfaceVersion,//
                           'merchID'        => $merchID,//
                           'orderid'           => $orderid, //
                           'orderDate'              => $orderDate,//
                           'orderTime'      => $orderTime, //
                           'tranType'     => ($tranType), //
                           'amount' => $amount,  //
                           'curType'    => $curType, //
                           'orderContent'             => ($orderContent) ,//
                           'orderMono'      => $orderMono,//
                           'phdFlag'          => $phdFlag,//
                           'notifyType'          => $notifyType,//
                           'merURL'          => $merURL,//
                           'goodsURL'          => $goodsURL,//
                           'jumpSeconds'          => $jumpSeconds,//
                           'payBatchNo'          => $payBatchNo,//
                           'proxyMerName'          => $proxyMerName,//
                           'proxyMerType'          => $proxyMerType,//
                           'proxyMerCredentials'          => $proxyMerCredentials,//
                           'netType'          => $netType,//
                           'issBankNo'          => $issBankNo,//
                           'signMsg_value'          => $signMsg_value //
                        );
        }
        return $parameter;
    }
    
	public function sign($prestr) {
		$mysign = md5($prestr);
		return $mysign;
	}
    
	public function para_filter($parameter) {
		$para = array();
		while (list ($key, $val) = each ($parameter)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para[$key] = $parameter[$key];

		}
		return $para;
	}
	
	public function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}

	public function charset_encode($input,$_output_charset ,$_input_charset ="GBK" ) {
		$output = "";
		if($_input_charset == $_output_charset || $input ==null) {
			$output = $input;
		} elseif (function_exists("mb_convert_encoding")){
			$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
		} elseif(function_exists("iconv")) {
			$output = iconv($_input_charset,$_output_charset,$input);
		} else die("sorry, you have no libs support for charset change.");
		return $output;
	}
   
	/**
	 * Return authorized languages by Bankcomm
	 *
	 * @param	none
	 * @return	array
	 */
	protected function _getAuthorizedLanguages()
	{
		$languages = array();
		
        foreach (Mage::getConfig()->getNode('global/payment/bankcomm_payment/languages')->asArray() as $data) 
		{
			$languages[$data['code']] = $data['name'];
		}
		
		return $languages;
	}
	
	/**
	 * Return language code to send to Bankcomm
	 *
	 * @param	none
	 * @return	String
	 */
	protected function _getLanguageCode()
	{
		// Store language
		$language = strtoupper(substr(Mage::getStoreConfig('general/locale/code'), 0, 2));

		// Authorized Languages
		$authorized_languages = $this->_getAuthorizedLanguages();

		if (count($authorized_languages) === 1) 
		{
			$codes = array_keys($authorized_languages);
			return $codes[0];
		}
		
		if (array_key_exists($language, $authorized_languages)) 
		{
			return $language;
		}
		
		// By default we use language selected in store admin
		return $this->getConfigData('language');
	}



    /**
     *  Output failure response and stop the script
     *
     *  @param    none
     *  @return	  void
     */
    public function generateErrorResponse()
    {
        die($this->getErrorResponse());
    }

    /**
     *  Return response for Bankcomm success payment
     *
     *  @param    none
     *  @return	  string Success response string
     */
    public function getSuccessResponse()
    {
        $response = array(
            'Pragma: no-cache',
            'Content-type : text/plain',
            'Version: 1',
            'OK'
        );
        return implode("\n", $response) . "\n";
    }

    /**
     *  Return response for Bankcomm failure payment
     *
     *  @param    none
     *  @return	  string Failure response string
     */
    public function getErrorResponse()
    {
        $response = array(
            'Pragma: no-cache',
            'Content-type : text/plain',
            'Version: 1',
            'Document falsifie'
        );
        return implode("\n", $response) . "\n";
    }

}