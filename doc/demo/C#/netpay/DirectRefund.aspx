<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head runat="server">
    <title>直接退款测试</title>
</head>
<body>
     <%
         string op = Request.Params.Get("operator");
         string order = Request.Params.Get("order");
         string refunddate = Request.Params.Get("date");
         string amount = Request.Params.Get("amount");
         string comment = Request.Params.Get("comment");
         string gatherFlag = Request.Params.Get("gatherFlag");
         string gatherNo = Request.Params.Get("gatherNo");
         string tranCode = "cb2212_DirectRefundOp";

         StringBuilder sendMsg = new StringBuilder("");
         
         //组织申请报文
         sendMsg.Append("<Message>")
                .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
                .Append("<MsgContent><BOCOMB2C><opName>").Append(tranCode)
                .Append("</opName><reqParam><merchantID>").Append(config.merchantID).Append("</merchantID>")
                .Append("<operator>").Append(op).Append("</operator><order>")
                .Append(order).Append("</order><date>")
                .Append(refunddate).Append("</date><amount>").Append(amount).Append("</amount><comment>")
                .Append(comment).Append("</comment><gatherFlag>").Append(gatherFlag).Append("</gatherFlag><gatherNo>")
                .Append(gatherNo).Append("</gatherNo>")
                .Append("</reqParam></BOCOMB2C></MsgContent></Message>");

       TcpClient client = new TcpClient(config.ip, config.port);
       NetworkStream stream = client.GetStream();

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
       string errMsg = "";
       if (list.Item(0) != null)
       {
           errMsg = list.Item(0).InnerText.Trim();
       }   
       
       Response.Write("交易返回码：" + retCode + "<br>");
       Response.Write("交易错误信息：" + errMsg + "<br>");
       
       if ("0".Equals(retCode))
       {
           XmlNodeList opResult = xmlDoc.GetElementsByTagName("opResult");
           string serial = opResult.Item(0).SelectSingleNode("serial").InnerText.Trim();
           string account = opResult.Item(0).SelectSingleNode("account").InnerText.Trim();

           Response.Write("退款流水号：" + serial + "<br>");
           Response.Write("退款账号：" + account + "<br>");
       }

    %>
</body>
</html>
