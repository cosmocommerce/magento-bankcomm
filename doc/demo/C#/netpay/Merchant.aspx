<%@ Page Language="C#"  ResponseEncoding="gbk" %>

<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head id="Head1" runat="server">
    <title>订单提交</title>
    <meta http-equiv = "Content-Type" content = "text/html; charset=gbk"/>
</head>
<body onload="form1.submit()">
   <%
      
       string interfaceVersion = Request.Params.Get("interfaceVersion");
       string orderid = Request.Params.Get("orderid");
       string orderDate = Request.Params.Get("orderDate");
       string orderTime = Request.Params.Get("orderTime");
       string tranType = Request.Params.Get("tranType");
       string amount = Request.Params.Get("amount");
       string curType = Request.Params.Get("curType");
       string orderContent = Request.Params.Get("orderContent");
       string orderMono = Request.Params.Get("orderMono");
       string phdFlag = Request.Params.Get("phdFlag");
       string notifyType = Request.Params.Get("notifyType");
       string merURL = Request.Params.Get("merURL");
       string goodsURL = Request.Params.Get("goodsURL");
       string jumpSeconds = Request.Params.Get("jumpSeconds");
       string payBatchNo = Request.Params.Get("payBatchNo");
       string proxyMerName = Request.Params.Get("proxyMername");
       string proxyMerType = Request.Params.Get("proxyMerType");
       string proxyMercredentials = Request.Params.Get("proxyMercredentials");
       string netType = Request.Params.Get("netType");
       string issBankNo = Request.Params.Get("issBankNo"); //不参与签名，规则同银联
       string tranCode = "cb2200_sign";
        
       string merID = config.merchantID;

       string sourceMsg = interfaceVersion + "|" + merID + "|" + orderid + "|" + orderDate + "|" + orderTime + "|" + tranType + "|" + amount + "|" + curType + "|" + orderContent + "|" + orderMono + "|" + phdFlag + "|" + notifyType + "|" + merURL + "|" + goodsURL + "|" + jumpSeconds + "|" + payBatchNo + "|" + proxyMerName + "|" + proxyMerType + "|" + proxyMercredentials + "|" + netType;

       StringBuilder sendMsg = new StringBuilder("");
       //sendMsg.Append("<?xml version='1.0' encoding='UTF-8'?>")
       //组织申请报文
       sendMsg.Append("<Message>")
              .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
              .Append("<MsgContent>")
              .Append(sourceMsg)
              .Append("</MsgContent></Message>");

       TcpClient client = new TcpClient(config.ip, config.port);
       NetworkStream stream = client.GetStream();
       
       //System.Text.Encoding.GetEncoding("GBK").
       
       Byte[] data = System.Text.Encoding.UTF8.GetBytes(sendMsg.ToString());
       stream.Write(data, 0, data.Length);
       data = new Byte[50 * 1024];
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
       
       list = xmlDoc.GetElementsByTagName("signMsg");
       string signMsg = list.Item(0).InnerText.Trim();
       
       list = xmlDoc.GetElementsByTagName("orderUrl");
       string orderUrl = list.Item(0).InnerText.Trim(); 
       
       if(!retCode.Equals("0"))
       {
           Response.Write("交易返回码：" + retCode + "<br>");
           Response.Write("交易错误信息：" + errMsg + "<br>");
       }
       else
       {
       
       

      %>
    
    <form name="form1" method="post" action ="<% Response.Write(orderUrl); %>">
        <input type = "hidden" name = "interfaceVersion" value = "<%Response.Write(interfaceVersion);%>"/>
			<input type = "hidden" name = "merID" value = "<%Response.Write(merID);%>"/>
			<input type = "hidden" name = "orderid" value = "<%Response.Write(orderid);%>"/>
			<input type = "hidden" name = "orderDate" value = "<%Response.Write(orderDate);%>"/>
			<input type = "hidden" name = "orderTime" value = "<%Response.Write(orderTime);%>"/>
			<input type = "hidden" name = "tranType" value = "<%Response.Write(tranType);%>"/>
			<input type = "hidden" name = "amount" value = "<%Response.Write(amount);%>"/>
			<input type = "hidden" name = "curType" value = "<%Response.Write(curType);%>"/>
			<input type = "hidden" name = "orderContent" value = "<%Response.Write(orderContent);%>"/>
			<input type = "hidden" name = "orderMono" value = "<%Response.Write(orderMono);%>"/>
			<input type = "hidden" name = "phdFlag" value = "<%Response.Write(phdFlag);%>"/>
			<input type = "hidden" name = "notifyType" value = "<%Response.Write(notifyType);%>"/>
			<input type = "hidden" name = "merURL" value = "<%Response.Write(merURL);%>"/>
			<input type = "hidden" name = "goodsURL" value = "<%Response.Write(goodsURL);%>"/>
			<input type = "hidden" name = "jumpSeconds" value = "<%Response.Write(jumpSeconds);%>"/>
			<input type = "hidden" name = "payBatchNo" value = "<%Response.Write(payBatchNo);%>"/>
			<input type = "hidden" name = "proxyMerName" value = "<%Response.Write(proxyMerName);%>"/>
			<input type = "hidden" name = "proxyMerType" value = "<%Response.Write(proxyMerType);%>"/>
			<input type = "hidden" name = "proxyMerCredentials" value = "<%Response.Write(proxyMercredentials);%>"/>
			<input type = "hidden" name = "netType" value = "<%Response.Write(netType);%>"/>
			<input type = "hidden" name = "merSignMsg" value = "<%Response.Write(signMsg);%>"/>
			<input type = "hidden" name = "issBankNo" value = "<%Response.Write(issBankNo);%>"/>
    
    </form>
    <%
       }
     %>
</body>
</html>
