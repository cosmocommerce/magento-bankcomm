<?php
// PHP version of Historyorderquery.jsp
//这是B2CAPI通用版的php客户端调用测试
//作    者：bocomm
//创建时间：2012-5-16
?>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head runat="server">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>退款明细查询</title>
</head>
<body>
<?php
	require_once ("config.inc");  //参数文件
	$merchID = $merchID; //商户号为固定
	$begDate = $_REQUEST["begDate"];
	$endDate  = $_REQUEST["endDate"];
	$flag = $_REQUEST["flag"];
	$begIndex = $_REQUEST["begIndex"];
	$refundtype = $_REQUEST["refundtype"];
	$order = $_REQUEST["order"];

	$tranCode = "cb2205_queryRefundOp";
	//连接地址
	$socketUrl = "tcp://".$socket_ip.":".$socket_port;
	$fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
	$retMsg="";
	//
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else {
		$in  = "<?xml version='1.0' encoding='UTF-8'?>";
		$in .= "<Message>";
		$in .= "<TranCode>".$tranCode."</TranCode>";
		$in .= "<MsgContent><BOCOMB2C><opName>".$tranCode."</opName><reqParam>";
		$in .= "<merchantID>".$merchID."</merchantID>";
		$in .= "<beginDate>".$begDate."</beginDate>";
		$in .= "<endDate>".$endDate."</endDate>";
		$in .= "<state>".$flag."</state>";
		$in .= "<beginIndex>".$begIndex."</beginIndex>";
		$in .= "<refundType>".$refundtype."</refundType>";
		$in .= "<order>".$order."</order>";
		$in .= "</reqParam></BOCOMB2C></MsgContent></Message>";
    fwrite($fp, $in);
    while (!feof($fp)) {
       $retMsg =$retMsg.fgets($fp, 1024);
    }
    fclose($fp);

	//解析返回xml
	$dom = new DOMDocument;
	$dom->loadXML($retMsg);

	$retCode = $dom->getElementsByTagName('retCode');
	$retCode_value = $retCode->item(0)->nodeValue;
	
	$errMsg = $dom->getElementsByTagName('errMsg');
	$errMsg_value = $errMsg->item(0)->nodeValue;
	
	echo "交易返回码：".$retCode_value."<br>";
	echo "交易错误信息：" .$errMsg_value."<br>";
	
	if($retCode_value == "0"){
		$BOCOMB2C = new SimpleXMLElement($retMsg);
		foreach($BOCOMB2C->opRep->opResultSet->opResult as $opResult){
			echo "流水号：".$opResult->serialno."<br>";
			echo "订单号：".$opResult->order."<br>";
			echo "订单日期：".$opResult->orderDate."<br>"; 
			echo "订单时间：".$opResult->orderTime."<br>";  
			echo "币种：".$opResult->curType."<br>";
			echo "订单金额：".$opResult->amount."<br>";
			echo "退款类型：".$opResult->refundType."<br>";
			echo "支付日期：".$opResult->tranDate."<br>";
			echo "支付时间：".$opResult->tranTime."<br>";
			echo "交易状态：".$opResult->tranState."<br>";
			echo "商户备注：".$opResult->merchantComment."<br>";
			echo "银行备注：".$opResult->bankComment."<br>";
			echo "手续费：".$opResult->fee."<br>";
			echo "退款账号：".$opResult->account."<br>";
			echo "<br>";
		}
	}
}
?> 
</body>
</html> 