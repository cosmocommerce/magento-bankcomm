﻿<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head id="Head1" runat="server">
    <title>重新发送短信验证身份</title>
</head>
<body>
     <%
         string sessionID = Request.Params.Get("sessionID");
         string tranCode = "PY0304";   //交易码
         
         StringBuilder sendMsg = new StringBuilder("");
         //sendMsg.Append("<?xml version='1.0' encoding='UTF-8'?>")
         //组织申请报文
         sendMsg.Append("<Message>")
                .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
                .Append("<ConfigPath>").Append(config.configPath).Append("</ConfigPath>")
                .Append("<TranFlag>").Append(config.shortPay).Append("</TranFlag>")
                .Append("<MsgContent><BOCOMB2C><opName>").Append(tranCode).Append("</opName><reqParam>")
                .Append("<merID>").Append(config.merchantID).Append("</merID>")
                .Append("<sessionID>").Append(sessionID).Append("</sessionID>")
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
             string sessionID1 = opResult.Item(0).SelectSingleNode("sessionID").InnerText.Trim();
             string sendCount = opResult.Item(0).SelectSingleNode("sendCount").InnerText.Trim();

             Response.Write("商户号：" + merID + "<br>");
             Response.Write("识别码：" + sessionID1 + "<br>");
             Response.Write("已重发次数：" + sendCount + "<br>");
         }
         
     %>
</body>
</html>
