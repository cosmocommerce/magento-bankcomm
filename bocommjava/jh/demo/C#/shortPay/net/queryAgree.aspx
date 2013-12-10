<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head runat="server">
    <title>协议查询测试</title>
</head>
<body>
     <%
         string merAgreeNo = Request.Params.Get("merAgreeNo");
         string begDate = Request.Params.Get("begDate");
         string endDate = Request.Params.Get("endDate");
         string begIndex = Request.Params.Get("begIndex");
         string tranType = Request.Params.Get("tranType");
         string tranCode = "PY0320";   //交易码
         
         StringBuilder sendMsg = new StringBuilder("");
         //sendMsg.Append("<?xml version='1.0' encoding='UTF-8'?>")
         //组织申请报文
         sendMsg.Append("<Message>")
                .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
                .Append("<TranFlag>").Append(config.shortPay).Append("</TranFlag>")
                .Append("<MsgContent><BOCOMB2C><opName>").Append(tranCode).Append("</opName><reqParam>")
                .Append("<merID>").Append(config.merchantID).Append("</merID>")
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

             XmlNodeList result = xmlDoc.GetElementsByTagName("opResult");
             
             string merchNo = result.Item(0).SelectSingleNode("merID").InnerText.Trim();
             string agreeNo = result.Item(0).SelectSingleNode("agreeNo").InnerText.Trim();
             merAgreeNo = result.Item(0).SelectSingleNode("merAgreeNo").InnerText.Trim();
             string cardNo = result.Item(0).SelectSingleNode("cardNo").InnerText.Trim();
             string cardType = result.Item(0).SelectSingleNode("cardType").InnerText.Trim();
             string state = result.Item(0).SelectSingleNode("state").InnerText.Trim();
             string merComment = result.Item(0).SelectSingleNode("merComment").InnerText.Trim();
            
             Response.Write("商户号：" + merchNo + "<br>");
             Response.Write("协议号：" + agreeNo + "<br>");
             Response.Write("协议检索号：" + merAgreeNo + "<br>");
             Response.Write("签约卡号：" + cardNo + "<br>");
             Response.Write("签约卡类型：" + cardType + "<br>");
             Response.Write("状态：" + state + "<br>");
             Response.Write("商户备注：" + merComment + "<br>");
            
             Response.Write("<br>");
             
         }
         
     %>
</body>
</html>
