<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head runat="server">
    <title>退款明细查询测试</title>
</head>
<body>
    <%
        string begDate = Request.Params.Get("begDate");
        string endDate = Request.Params.Get("endDate");
        string flag = Request.Params.Get("flag");
        string begIndex = Request.Params.Get("begIndex");
        string refundtype = Request.Params.Get("refundtype");
        string order = Request.Params.Get("order");
        string tranCode = "cb2205_queryRefundOp";

        StringBuilder sendMsg = new StringBuilder("");

        //组织申请报文
        sendMsg.Append("<Message>")
               .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
               .Append("<MsgContent><BOCOMB2C><opName>").Append(tranCode).Append("</opName><reqParam><merchantID>")
               .Append(config.merchantID).Append("</merchantID>")
               .Append("<beginDate>").Append(begDate).Append("</beginDate><endDate>")
               .Append(endDate).Append("</endDate><refundType>").Append(refundtype).Append("</refundType><order>")
               .Append(order).Append("</order><state>").Append(flag).Append("</state><beginIndex>")
               .Append(begIndex).Append("</beginIndex>")
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

            XmlNodeList result = xmlDoc.GetElementsByTagName("opResult");
            string merchantID = result.Item(0).SelectSingleNode("merchantID").InnerText.Trim();
            string totalNumber = result.Item(0).SelectSingleNode("totalNumber").InnerText.Trim();
            string beginIndex;
            if (int.Parse(totalNumber) > 20)
            {
                beginIndex = "20";
            }
            else
            {
                beginIndex = totalNumber;
            }
            
            Response.Write("总交易记录数：" + totalNumber + "<br>");
            Response.Write("本次返回记录数：" + beginIndex + "<br>");

        
            XmlNode opResultSet = xmlDoc.GetElementsByTagName("opResultSet").Item(0);
            XmlNodeList opResult = opResultSet.SelectNodes("opResult");
            for (int i = 0; i < opResult.Count; i++)
            {
                string serialno = opResult.Item(i).SelectSingleNode("serialno").InnerText.Trim();
                string order1 = opResult.Item(i).SelectSingleNode("order").InnerText.Trim();
                string orderDate = opResult.Item(i).SelectSingleNode("orderDate").InnerText.Trim();
                string orderTime = opResult.Item(i).SelectSingleNode("orderTime").InnerText.Trim();
                string curType = opResult.Item(i).SelectSingleNode("curType").InnerText.Trim();
                string amount = opResult.Item(i).SelectSingleNode("amount").InnerText.Trim();
                string refundType = opResult.Item(i).SelectSingleNode("refundType").InnerText.Trim();
                string tranDate = opResult.Item(i).SelectSingleNode("tranDate").InnerText.Trim();
                string tranTime = opResult.Item(i).SelectSingleNode("tranTime").InnerText.Trim();
                string tranState = opResult.Item(i).SelectSingleNode("tranState").InnerText.Trim();
                string merchantComment;
                if (!opResult.Item(i).Name.Equals("merchantComment"))
                {
                    merchantComment = "";
                }
                else
                {
                    merchantComment = opResult.Item(i).SelectSingleNode("merchantComment").InnerText.Trim();
                }

                string bankComment;
                if (!opResult.Item(i).Name.Equals("bankComment"))
                {
                    bankComment = "";
                }
                else
                {
                    bankComment = opResult.Item(i).SelectSingleNode("bankComment").InnerText.Trim();
                }
                
                string fee = opResult.Item(i).SelectSingleNode("fee").InnerText.Trim();
                string account = opResult.Item(i).SelectSingleNode("account").InnerText.Trim();

                Response.Write("流水号：" + serialno + "<br>");
                Response.Write("订单号：" + order1 + "<br>");
                Response.Write("订单日期：" + orderDate + "<br>");
                Response.Write("订单时间：" + orderTime + "<br>");
                Response.Write("币种：" + curType + "<br>");
                Response.Write("订单金额：" + amount + "<br>");
                Response.Write("退款类型：" + refundType + "<br>");
                Response.Write("支付日期：" + tranDate + "<br>");
                Response.Write("支付时间：" + tranTime + "<br>");
                Response.Write("交易状态：" + tranState + "<br>");
                Response.Write("商户备注：" + merchantComment + "<br>");
                Response.Write("银行备注：" + bankComment + "<br>");
                Response.Write("手续费：" + fee + "<br>");
                Response.Write("退款账号：" + account + "<br>");
                Response.Write("<br>");
            }
        }
     %>
    
</body>
</html>
