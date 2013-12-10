<?php
// PHP version of Historyorderquery.jsp
//这是B2CAPI通用版的php客户端调用测试
//作    者：bocomm
//创建时间：2012-5-16
?>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head runat="server">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>历史订单查询</title>
</head>
<body>
<?php
	require_once ("config.inc");  //参数文件
	$merchID = $merchID; //商户号为固定
	$begDate = $_REQUEST["begDate"];
	$endDate = $_REQUEST["endDate"];
	$flag = $_REQUEST["flag"];
	$begIndex = $_REQUEST["begIndex"];
	$begOrder = $_REQUEST["begOrder"];
	$endOrder = $_REQUEST["endOrder"];
	$sortField = $_REQUEST["sortField"];
	$sortOrder = $_REQUEST["sortOrder"];

	$tranCode = "cb2204_queryHistoryOrderOp";
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
		$in .= "<flag>".$flag."</flag>";
		$in .= "<beginIndex>".$begIndex."</beginIndex>";
		$in .= "<beginOrder>".$begOrder."</beginOrder>";
		$in .= "<endOrder>".$endOrder."</endOrder>";
		$in .= "<sortField>".$sortField."</sortField>";
		$in .= "<sortOrder>".$sortOrder."</sortOrder>";
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
			echo "订单号：".$opResult->order."<br>";
			echo "订单日期：".$opResult->orderDate."<br>";
			echo "订单时间：".$opResult->orderTime."<br>"; 
			echo "币种：".$opResult->curType."<br>";  
			echo "金额：".$opResult->amount."<br>";
			echo "交易日期：".$opResult->tranDate."<br>";
			echo "交易时间：".$opResult->tranTime."<br>";
			echo "支付交易状态：".$opResult->tranState."<br>";
			echo "定单状态[0 所有 1 已支付 2 已撤销 3 部分退货 4退货处理中 5 全部退货]：".$opResult->orderState."<br>";
			echo "手续费：".$opResult->fee."<br>";
			echo "银行流水号：".$opResult->bankSerialNo."<br>";
			echo "银行批次号：".$opResult->bankBatNo."<br>";
			echo "交易卡类型[0:借记卡 1：准贷记卡 2:贷记卡]：".$opResult->cardType."<br>";
			echo "商户批次号：".$opResult->merchantBatNo."<br>";
			echo "商户备注：".$opResult->merchantComment."<br>";
			echo "银行备注：".$opResult->bankComment."<br>";
			echo "<br>";
		}
	}
}
?> 
</body>
</html>