<?php
// PHP version of merchant_result.jsp
//����B2CAPIͨ�ð��php�ͻ��˵��ò���
//��    �ߣ�bocomm
//����ʱ�䣺2012-4-10
?>
<html>
    <head>
        <title>��ͨ�����̻����Խ��ҳ��</title>

        <meta http-equiv = "Content-Type" content = "text/html; charset=GBK">
    </head>


    <body bgcolor = "#FFFFFF" text = "#000000">

<?php
	require_once ("config.inc");  //�����ļ�
	$tranCode = "cb2200_verify";
	$notifyMsg = $_REQUEST["notifyMsg"];   
	$lastIndex = strripos($notifyMsg,"|");
	$signMsg = substr($notifyMsg,$lastIndex+1); //ǩ����Ϣ
	$srcMsg = substr($notifyMsg,0,$lastIndex+1);//ԭ��

	//���ӵ�ַ
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
	
	//��������xml
	$dom = new DOMDocument;
	$dom->loadXML($retMsg);

	$retCode = $dom->getElementsByTagName('retCode');
	$retCode_value = $retCode->item(0)->nodeValue;
	
	$errMsg = $dom->getElementsByTagName('errMsg');
	$errMsg_value = $errMsg->item(0)->nodeValue;

	//echo "retCode=".$retCode_value."  "."errMsg=".$errMsg_value;
	if($retCode_value != '0')
       {
           echo "���׷����룺".$retCode_value."<br>";
           echo "���״�����Ϣ��" .$errMsg_value."<br>";
       }
       else
       {
		   $arr = preg_split("/\|{1,}/",$srcMsg);

?> 

        <table width = "75%" border = "0" cellspacing = "0" cellpadding = "0">
            <tr>
                <td width = "14%">
                    �̻��ͻ���
                </td>

                <td width = "86%">

                    <?php
                    print $arr[0];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    �������
                </td>

                <td width = "86%">

                   <?php
                    print $arr[1];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���׽��
                </td>

                <td width = "86%">

                   <?php
                    print $arr[2];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���ױ���
                </td>

                <td width = "86%">

                  <?php
                    print $arr[3];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ƽ̨���κ�
                </td>

                <td width = "86%">

                   <?php
                    print $arr[4];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    �̻����κ�
                </td>

                <td width = "86%">

                   <?php
                    print $arr[5];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ��������
                </td>

                <td width = "86%">

                    <?php
                    print $arr[6];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ����ʱ��
                </td>

                <td width = "86%">

                    <?php
                    print $arr[7];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ������ˮ��
                </td>

                <td width = "86%">

                    <?php
                    print $arr[8];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���׽��
                </td>

                <td width = "86%">
                   <?php
                    print $arr[9];
                    ?>

                    &nbsp;[1:�ɹ�]
                </td>
            </tr>

            <tr>
                <td width = "14%">
                    �������ܶ�
                </td>

                <td width = "86%">

                   <?php
                    print $arr[10];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���п�����
                </td>

                <td width = "86%">
                   <?php
                    print $arr[11];
                    ?>

                    &nbsp;[0:��ǿ� 1��׼���ǿ� 2:���ǿ�]
                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���б�ע
                </td>

                <td width = "86%">

                    <?php
                    print $arr[12];
                    ?>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ������Ϣ����
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
                    �̻���ע(base64������ַ���������Ҫ���ص���Ҫbase64����ԭ��)
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