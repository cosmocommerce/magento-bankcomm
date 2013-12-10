<?php
// PHP version of merchant.jsp
//这是B2CAPI通用版的php客户端调用测试
//作    者：bocomm
//创建时间：2012-5-16
?>

<?php
	require_once ("config.inc");  //参数文件
	//获得表单传过来的数据
	$interfaceVersion = $_REQUEST["interfaceVersion"];		
	$merID = $merchID; //商户号为固定
	$orderid = $_REQUEST["orderid"];
	$orderDate = $_REQUEST["orderDate"];
	$orderTime = $_REQUEST["orderTime"];
	$tranType = $_REQUEST["tranType"];
	$amount = $_REQUEST["amount"];
	$curType = $_REQUEST["curType"];
	$orderContent = $_REQUEST["orderContent"];
	$orderMono = $_REQUEST["orderMono"];
	$phone = $_REQUEST["phone"];
	$period = $_REQUEST["period"];
	$phdFlag = $_REQUEST["phdFlag"];
	$notifyType = $_REQUEST["notifyType"];
	$merURL = $_REQUEST["merURL"];
	$goodsURL = $_REQUEST["goodsURL"];
	$jumpSeconds = $_REQUEST["jumpSeconds"];
	$payBatchNo = $_REQUEST["payBatchNo"];
	$proxyMerName = $_REQUEST["proxyMerName"];
	$proxyMerType = $_REQUEST["proxyMerType"];
	$proxyMerCredentials = $_REQUEST["proxyMerCredentials"];
	$netType = $_REQUEST["netType"];
	$tranCode = "cb2210_create_orderOp";

	$source = "";

	//连接地址
	$socketUrl = "tcp://".$socket_ip.":".$socket_port;
	$fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
	$retMsg="";
	
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else {
		//组织申请报文
	
		
		$in  = "<?xml version='1.0' encoding='UTF-8'?>";
		$in .= "<Message>";
		   
		$in .= "<TranCode>".$tranCode."</TranCode>";
		  
		$in .= "<MsgContent><BOCOMB2C><opName>".$tranCode."</opName><reqParam>";
		   
		$in .= "<merchantID>".$merchID."</merchantID>";
		   
		$in .= "<interfaceVersion>".$interfaceVersion."</interfaceVersion>";
		   
		$in .= "<merID>".$merchID."</merID>";
		   
		$in .= "<orderID>".$orderid."</orderID>";
		   
		$in .= "<orderDate>".$orderDate."</orderDate>";
		   
		$in .= "<orderTime>".$orderTime."</orderTime>";
		   
		$in .= "<tranType>".$tranType."</tranType>";
		  
		$in .= "<amount>".$amount."</amount>";
		   
		$in .= "<curType>".$curType."</curType>";
		
		$in .= "<orderContent>".$orderContent."</orderContent>";
	
		$in .= "<orderMono>".$orderMono."</orderMono>";
		 
		$in .= "<phone>".$phone."</phone>";
		  
		$in .= "<period>".$period."</period>";
		 
		$in .= "<phdFlag>".$phdFlag."</phdFlag>";
		
		$in .= "<notifyType>".$notifyType."</notifyType>";
		
		$in .= "<merURL>".$merURL."</merURL>";
		
		$in .= "<goodsURL>".$goodsURL."</goodsURL>";
		
		$in .= "<jumpSeconds>".$jumpSeconds."</jumpSeconds>";
		
		$in .= "<payBatchNo>".$payBatchNo."</payBatchNo>";
		
		$in .= "<proxyMerName>".$proxyMerName."</proxyMerName>";
	
		$in .= "<proxyMerType>".$proxyMerType."</proxyMerType>";
	
		$in .= "<proxyMerCredentials>".$proxyMerCredentials."</proxyMerCredentials>";
	
		$in .= "<netType>".$netType."</netType>";
		 
		$in .= "</reqParam></BOCOMB2C></MsgContent></Message>";

//*
    fwrite($fp, $in);
    while (!feof($fp)) {
       $retMsg =$retMsg.fgets($fp, 1024);
		}
    
    fclose($fp);
//*/
	//解析返回xml
	$dom = new DOMDocument;
	$dom->loadXML($retMsg);

	$retCode = $dom->getElementsByTagName('retCode');
	$retCode_value = $retCode->item(0)->nodeValue;
	
	$errMsg = $dom->getElementsByTagName('errMsg');
	$errMsg_value = $errMsg->item(0)->nodeValue;

	
	echo "交易返回码：".$retCode_value."<br>";
	echo "交易错误信息：" .$errMsg_value."<br>";
}
?>