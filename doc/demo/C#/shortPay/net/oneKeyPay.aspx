<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head id="Head1" runat="server">
    <title>一键直付版支付</title>
</head>
<body>
     <%
         string interfaceVersion = Request.Params.Get("interfaceVersion");
         string agreeNo = Request.Params.Get("agreeNo");
         string cardExpDate = Request.Params.Get("cardExpDate");
         string orderid = Request.Params.Get("orderid");
         string orderDate = Request.Params.Get("orderDate");
         string orderTime = Request.Params.Get("orderTime");
         string tranType = Request.Params.Get("tranType");
         string amount = Request.Params.Get("amount");
         string curType = Request.Params.Get("curType");
         string orderContent = Request.Params.Get("orderContent");
         string accNo = Request.Params.Get("accNo");
         string accName = Request.Params.Get("accName");
         string orderMono = Request.Params.Get("orderMono");
         string phdFlag = Request.Params.Get("phdFlag");
         string notifyType = Request.Params.Get("notifyType");
         string merURL = Request.Params.Get("merURL");
         string payBatchNo = Request.Params.Get("payBatchNo");
         string proxyMerName = Request.Params.Get("proxyMerName");
         string proxyMerType = Request.Params.Get("proxyMerType");
         string proxyMerCredentials = Request.Params.Get("proxyMerCredentials");
         string netType = Request.Params.Get("netType");
         string merAgreeNo = Request.Params.Get("merAgreeNo");

         string tranCode = "PY0309";   //交易码
         
         StringBuilder sendMsg = new StringBuilder("");
         //sendMsg.Append("<?xml version='1.0' encoding='UTF-8'?>")
         //组织申请报文
         sendMsg.Append("<Message>")
                .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
                .Append("<ConfigPath>").Append(config.configPath).Append("</ConfigPath>")
                .Append("<TranFlag>").Append(config.shortPay).Append("</TranFlag>")
                .Append("<MsgContent><BOCOMB2C><opName>").Append(tranCode).Append("</opName><reqParam>")
                .Append("<merID>").Append(config.merchantID).Append("</merID>")
                .Append("<interfaceVersion>").Append(interfaceVersion).Append("</interfaceVersion>")
                .Append("<agreeNo>").Append(agreeNo).Append("</agreeNo>")
                .Append("<cardNo>").Append(accNo).Append("</cardNo>")
                .Append("<cardExpDate>").Append(cardExpDate).Append("</cardExpDate>")
                .Append("<custName>").Append(accName).Append("</custName>")
                .Append("<orderNo>").Append(orderid).Append("</orderNo>")
                .Append("<orderDate>").Append(orderDate).Append("</orderDate>")
                .Append("<orderTime>").Append(orderTime).Append("</orderTime>")
                .Append("<tranType>").Append(tranType).Append("</tranType>")
                .Append("<amount>").Append(amount).Append("</amount>")
                .Append("<curType>").Append(curType).Append("</curType>")
                .Append("<orderContent>").Append(orderContent).Append("</orderContent>")
                .Append("<orderMono>").Append(orderMono).Append("</orderMono>")
                .Append("<phdFlag>").Append(phdFlag).Append("</phdFlag>")
                .Append("<notifyType>").Append(notifyType).Append("</notifyType>")
                .Append("<merURL>").Append(merURL).Append("</merURL>")
                .Append("<payBatchNo>").Append(payBatchNo).Append("</payBatchNo>")
                .Append("<proxyMerName>").Append(proxyMerName).Append("</proxyMerName>")
                .Append("<proxyMerType>").Append(proxyMerType).Append("</proxyMerType>")
                .Append("<proxyMerCredentials>").Append(proxyMerCredentials).Append("</proxyMerCredentials>")
                .Append("<netType>").Append(netType).Append("</netType>")
                .Append("<merAgreeNo>").Append(merAgreeNo).Append("</merAgreeNo>")
                .Append("</reqParam></BOCOMB2C></MsgContent></Message>");

         TcpClient client = new TcpClient(config.ip, config.port);
         NetworkStream stream = client.GetStream();

         Byte[] data = System.Text.Encoding.UTF8.GetBytes(sendMsg.ToString());
         stream.Write(data, 0, data.Length);
         data = new Byte[50*1024];
         String responseData = String.Empty;
         Int32 bytes = stream.Read(data, 0, data.Length);
         responseData = System.Text.Encoding.UTF8.GetString(data, 0, bytes);
         stream.Close();
         client.Close();
               
         //解析返回报文
         XmlDocument xmlDoc = new XmlDocument();
         xmlDoc.LoadXml(responseData);
         XmlNodeList list = xmlDoc.GetElementsByTagName("retCode");       
         string retCode = list.Item(0).InnerText.Trim();
         
         list = xmlDoc.GetElementsByTagName("errMsg");       
         string errMsg = list.Item(0).InnerText.Trim();

         Response.Write("交易返回码：" + retCode + "<br>");
         Response.Write("交易错误信息：" + errMsg + "<br>");

         if ("000000".Equals(retCode))
         {
             XmlNodeList opResult = xmlDoc.GetElementsByTagName("opResult");
             string merID = opResult.Item(0).SelectSingleNode("merID").InnerText.Trim();
             string orderNo = opResult.Item(0).SelectSingleNode("orderNo").InnerText.Trim();
             string amount1 = opResult.Item(0).SelectSingleNode("amount").InnerText.Trim();
             string curType1 = opResult.Item(0).SelectSingleNode("curType").InnerText.Trim();
             string batchNo = opResult.Item(0).SelectSingleNode("batchNo").InnerText.Trim();
             string payBatchNo1 = opResult.Item(0).SelectSingleNode("payBatchNo").InnerText.Trim();
             string tranDate = opResult.Item(0).SelectSingleNode("tranDate").InnerText.Trim();
             string tranTime = opResult.Item(0).SelectSingleNode("tranTime").InnerText.Trim();
             string serialNo = opResult.Item(0).SelectSingleNode("serialNo").InnerText.Trim();
             string tranState = opResult.Item(0).SelectSingleNode("tranState").InnerText.Trim();
             string feeSum = opResult.Item(0).SelectSingleNode("feeSum").InnerText.Trim();
             string cardType = opResult.Item(0).SelectSingleNode("cardType").InnerText.Trim();
             string bankcomment = opResult.Item(0).SelectSingleNode("bankcomment").InnerText.Trim();

             Response.Write("商户号：" + merID + "<br>");
             Response.Write("订单号：" + orderNo + "<br>");
             Response.Write("金额：" + amount1 + "<br>");
             Response.Write("币种：" + curType1 + "<br>");
             Response.Write("平台批次号：" + batchNo + "<br>");
             Response.Write("商户批次号：" + payBatchNo1 + "<br>");
             Response.Write("交易日期：" + tranDate + "<br>");
             Response.Write("交易时间：" + tranTime + "<br>");
             Response.Write("交易流水号：" + serialNo + "<br>");
             Response.Write("交易结果：" + tranState + "<br>");
             Response.Write("手续费总额：" + feeSum + "<br>");
             Response.Write("银行卡类型：" + cardType + "<br>");
             Response.Write("银行备注：" + bankcomment + "<br>");
         }
         
     %>
</body>
</html>
