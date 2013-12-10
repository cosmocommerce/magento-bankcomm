<?php
// PHP version of VerifyID.jsp
//这是B2CAPI通用版的php客户端调用测试
//作    者：bocomm
//创建时间：2012-4-10
?>
<html xmlns="http://www.w3.org/1999/xhtml" >
<head runat="server">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>持卡人身份验证(VIP商户)</title>
</head>
<body>
<?php
	require_once ("config.inc");  //参数文件
	$merchID = $merchID; //商户号为固定
	$card = $_REQUEST["card"];
	$custName = $_REQUEST["custName"];
	$certType = $_REQUEST["certType"];
	$certNo = $_REQUEST["certNo"];
	$tranCode = "cb2208_verifyCustIDOp";
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
		$in .= "<cardNo>".$card."</cardNo>";
		$in .= "<custName>".$custName."</custName>";
		$in .= "<certType>".$certType."</certType>";
		$in .= "<certNo>".$certNo."</certNo>";
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
	echo "交易错误信息：".$errMsg_value."<br>";
      
}
?> 
</body>
</html> 