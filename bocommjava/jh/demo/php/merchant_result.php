<?php
// PHP version of merchant_result.jsp
//这是B2CAPI通用版的php客户端调用测试
//作    者：bocomm
//创建时间：2012-4-10
?>
<html>
    <head>
        <title>交通银行商户测试结果页面</title>

        <meta http-equiv = "Content-Type" content = "text/html; charset=GBK">
    </head>


    <body bgcolor = "#FFFFFF" text = "#000000">

<?php
	require_once ("config.inc");  //参数文件
	$tranCode = "cb2200_verify";
	$notifyMsg = $_REQUEST["notifyMsg"];   
	$lastIndex = strripos($notifyMsg,"|");
	$signMsg = substr($notifyMsg,$lastIndex+1); //签名信息
	$srcMsg = substr($notifyMsg,0,$lastIndex+1);//原文

	//连接地址
	$socketUrl = "tcp://".$socket_ip.":".$socket_port;
	$fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
	$retMsg="";
	//
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else 
	{
		$in  = "<?xml version='1.0' encoding='UTF-8'?>";
		$in .= "<Message>";
		$in .= "<TranCode>".$tranCode."</TranCode>";
		$in .= "<MsgContent>".$notifyMsg."</MsgContent>";
		$in .= "</Message>";
		fwrite($fp, $in);
		while (!feof($fp)) {
			$retMsg =$retMsg.fgets($fp, 1024);
			
		}
		fclose($fp);
	}	
	
	//解析返回xml
	$dom = new DOMDocument;
	$dom->loadXML($retMsg);

	$retCode = $dom->getElementsByTagName('retCode');
	$retCode_value = $retCode->item(0)->nodeValue;
	
	$errMsg = $dom->getElementsByTagName('errMsg');
	$errMsg_value = $errMsg->item(0)->nodeValue;

	//echo "retCode=".$retCode_value."  "."errMsg=".$errMsg_value;
	if($retCode_value != '0')
       {
           echo "交易返回码：".$retCode_value."<br>";
           echo "交易错误信息：" .$errMsg_value."<br>";
       }
       else
       {
		   $arr = preg_split("/\|{1,}/",$srcMsg);

?> 

        <table width = "75%" border = "0" cellspacing = "0" cellpadding = "0">
            <tr>
                <td width = "14%">
                    商户客户号
                </td>

                <td width = "86%">

                    <?php
                    print $arr[0];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    订单编号
                </td>

                <td width = "86%">

                   <?php
                    print $arr[1];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易金额
                </td>

                <td width = "86%">

                   <?php
                    print $arr[2];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易币种
                </td>

                <td width = "86%">

                  <?php
                    print $arr[3];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    平台批次号
                </td>

                <td width = "86%">

                   <?php
                    print $arr[4];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    商户批次号
                </td>

                <td width = "86%">

                   <?php
                    print $arr[5];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易日期
                </td>

                <td width = "86%">

                    <?php
                    print $arr[6];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易时间
                </td>

                <td width = "86%">

                    <?php
                    print $arr[7];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易流水号
                </td>

                <td width = "86%">

                    <?php
                    print $arr[8];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易结果
                </td>

                <td width = "86%">
                   <?php
                    print $arr[9];
                    ?>

                    &nbsp;[1:成功]
                </td>
            </tr>

            <tr>
                <td width = "14%">
                    手续费总额
                </td>

                <td width = "86%">

                   <?php
                    print $arr[10];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    银行卡类型
                </td>

                <td width = "86%">
                   <?php
                    print $arr[11];
                    ?>

                    &nbsp;[0:借记卡 1：准贷记卡 2:贷记卡]
                </td>
            </tr>

            <tr>
                <td width = "14%">
                    银行备注
                </td>

                <td width = "86%">

                    <?php
                    print $arr[12];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    错误信息描述
                </td>

                <td width = "86%">
                   <?php
                    print $arr[13];
                    ?>

                </td>
            </tr>

			 <tr>
                <td width = "14%">
                    IP
                </td>

                <td width = "86%">

                    <?php
                    print $arr[14];
                    ?>

                </td>
            </tr>
            <tr>
                <td width = "14%">
                    Referer
                </td>

                <td width = "86%">

                     <?php
                    print $arr[15];
                    ?>

                </td>
            </tr>
             <tr>
                <td width = "14%">
                    商户备注(base64编码的字符串，如需要返回的需要base64解码原文)
                </td>

                <td width = "86%">

                   <?php
                    print $arr[16];
                    ?>


                </td>
            </tr>
        </table>
		<?php
			}
		?>
        <p>
            &nbsp;
        </p>
    </body>
</html>