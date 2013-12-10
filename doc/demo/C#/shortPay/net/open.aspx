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
<body onload="form1.submit()">
     <%
         string interfaceVersion = Request.Params.Get("interfaceVersion");
         string merAgreeNo = Request.Params.Get("merAgreeNo");
         string accName = Request.Params.Get("accName");
         string certType = Request.Params.Get("certType");
         string certNo = Request.Params.Get("certNo");
         string merURL = Request.Params.Get("merURL");
         string notifyURL = Request.Params.Get("notifyURL");
         string merComment = Request.Params.Get("merComment");
         string onekeyTranType = Request.Params.Get("onekeyTranType");
         string netType = Request.Params.Get("netType");
         string tranCode = "cb2200_sign";   //交易码
         string merID = config.merchantID;

         string sourceMsg = interfaceVersion + "|" + merAgreeNo + "|" + merID + "|" +accName + "|" + certType + "|" + certNo + "|" + merURL + "|" + notifyURL + "|" + merComment + "|" + onekeyTranType + "|" + netType;
         
         StringBuilder sendMsg = new StringBuilder("");
         //sendMsg.Append("<?xml version='1.0' encoding='UTF-8'?>")
         //组织申请报文
         sendMsg.Append("<Message>")
                .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
                .Append("<ConfigPath>").Append(config.configPath).Append("</ConfigPath>")
                .Append("<TranFlag>").Append(config.shortPay).Append("</TranFlag>")
                .Append("<MsgContent>").Append(sourceMsg).Append("</MsgContent></Message>");

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
		<input type = "hidden" name = "merAgreeNo" value = "<%Response.Write(merAgreeNo);%>"/>
		<input type = "hidden" name = "accName" value = "<%Response.Write(accName);%>"/>
		<input type = "hidden" name = "certType" value = "<%Response.Write(certType);%>"/>
		<input type = "hidden" name = "certNo" value = "<%Response.Write(certNo);%>"/>
		<input type = "hidden" name = "merURL" value = "<%Response.Write(merURL);%>"/>
		<input type = "hidden" name = "notifyURL" value = "<%Response.Write(notifyURL);%>"/>
		<input type = "hidden" name = "merComment" value = "<%Response.Write(merComment);%>"/>
		<input type = "hidden" name = "onekeyTranType" value = "<%Response.Write(onekeyTranType);%>"/>
		<input type = "hidden" name = "netType" value = "<%Response.Write(netType);%>"/>
		<input type = "hidden" name = "signData" value = "<%Response.Write(signMsg);%>"/>
		<input type = "hidden" name = "netType" value = "<%Response.Write(netType);%>"/>
    
    </form>
    <%
       }
     %>
</body>
</html>