<?php
// PHP version of DownloadSettlement.jsp
//这是B2CAPI通用版的php客户端调用测试
//作    者：bocomm
//创建时间：2012-5-16
?>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head runat="server">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>对账单下载测试</title>
</head>
<body>
<?php
	require_once ("config.inc");  //参数文件
	$merchID = $merchID; //商户号为固定
	$settleDate = $_REQUEST["settleDate"];
	$tranCode = "cb2207_downLoadSettlementOp";
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
		$in .= "<merchantID>".$merchID."</merchantID><date>".$settleDate."</date></reqParam></BOCOMB2C></MsgContent>";
		$in .= "</Message>";
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
		echo "总金额：".$BOCOMB2C->opRep->opResult->totalSum."<br>";
		echo "总笔数：".$BOCOMB2C->opRep->opResult->totalNumber."<br>";
		echo "总手续费：".$BOCOMB2C->opRep->opResult->totalFee."<br>";
	}
}
?> 
</body>
</html>