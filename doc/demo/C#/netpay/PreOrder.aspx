<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head id="Head1" runat="server">
    <title>预订单测试</title>
</head>
<body>
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
        string phone = Request.Params.Get("phone");
        string period = Request.Params.Get("period");
        string phdFlag = Request.Params.Get("phdFlag");
        string notifyType = Request.Params.Get("notifyType");
        string merURL = Request.Params.Get("merURL");
        string goodsURL = Request.Params.Get("goodsURL");
        string jumpSeconds = Request.Params.Get("jumpSeconds");
        string payBatchNo = Request.Params.Get("payBatchNo");
        string proxyMerName = Request.Params.Get("proxyMerName");
        string proxyMerType = Request.Params.Get("proxyMerType");
        string proxyMerCredentials = Request.Params.Get("proxyMerCredentials");
        string netType = Request.Params.Get("netType");
        string tranCode = "cb2210_create_orderOp";
  
        StringBuilder sendMsg = new StringBuilder("");

        //组织申请报文
        sendMsg.Append("<Message>")
               .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
               .Append("<MsgContent><BOCOMB2C><opName>").Append(tranCode).Append("</opName><reqParam><merchantID>")
               .Append(config.merchantID).Append("</merchantID>")
               .Append("<interfaceVersion>").Append(interfaceVersion).Append("</interfaceVersion>")
               .Append("<merID>").Append(config.merchantID).Append("</merID>")
               .Append("<orderID>").Append(orderid).Append("</orderID>")
               .Append("<orderDate>").Append(orderDate).Append("</orderDate>")
               .Append("<orderTime>").Append(orderTime).Append("</orderTime>")
               .Append("<tranType>").Append(tranType).Append("</tranType>")
               .Append("<amount>").Append(amount).Append("</amount>")
               .Append("<curType>").Append(curType).Append("</curType>")
               .Append("<orderContent>").Append(orderContent).Append("</orderContent>")
               .Append("<orderMono>").Append(orderMono).Append("</orderMono>")
               .Append("<phone>").Append(phone).Append("</phone>")
               .Append("<period>").Append(period).Append("</period>")
               .Append("<phdFlag>").Append(phdFlag).Append("</phdFlag>")
               .Append("<notifyType>").Append(notifyType).Append("</notifyType>")
               .Append("<merURL>").Append(merURL).Append("</merURL>")
               .Append("<goodsURL>").Append(goodsURL).Append("</goodsURL>")
               .Append("<jumpSeconds>").Append(jumpSeconds).Append("</jumpSeconds>")
               .Append("<payBatchNo>").Append(payBatchNo).Append("</payBatchNo>")
               .Append("<proxyMerName>").Append(proxyMerName).Append("</proxyMerName>")
               .Append("<proxyMerType>").Append(proxyMerType).Append("</proxyMerType>")
               .Append("<proxyMerCredentials>").Append(proxyMerCredentials).Append("</proxyMerCredentials>")
               .Append("<netType>").Append(netType).Append("</netType>")
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
        string errMsg = list.Item(0).InnerText.Trim();
        list = xmlDoc.GetElementsByTagName("signMsg");

        Response.Write("交易返回码：" + retCode + "<br>");
        Response.Write("交易错误信息：" + errMsg + "<br>");

    %>
     
</body>
</html>
