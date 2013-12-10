<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head runat="server">
    <title>商户通知</title>
</head>
<body>
   <%
      
           string tranCode = "cb2200_verify";
           string notifyMsg = Request.Params.Get("notifyMsg");
           //vip商户通知需要做urldecode处理 
           Encoding gbk = Encoding.GetEncoding("GBK");
           notifyMsg = HttpUtility.UrlDecode(notifyMsg, gbk);      
       
           StringBuilder sendMsg = new StringBuilder("");
           //sendMsg.Append("<?xml version='1.0' encoding='UTF-8'?>")
           //组织申请报文
           sendMsg.Append("<Message>")
                  .Append("<TranCode>").Append(tranCode).Append("</TranCode>")
                  .Append("<MsgContent>")
                  .Append(notifyMsg)
                  .Append("</MsgContent></Message>");

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
                     
           if(!retCode.Equals("0"))
           {
               Response.Write("交易返回码：" + retCode + "<br>");
               Response.Write("交易错误信息：" + errMsg + "<br>");
           }                  
           else
           {
               string[] strs = notifyMsg.Split('|');
       %>
              <table width = "75%" border = "0" cellspacing = "0" cellpadding = "0">
            <tr>
                <td width = "14%">
                    商户客户号
                </td>

                <td width = "86%">

                    <%
                    Response.Write(strs[0]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    订单编号
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[1]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易金额
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[2]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易币种
                </td>

                <td width = "86%">

                    <%
                    Response.Write(strs[3]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    平台批次号
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[4]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    商户批次号
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[5]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易日期
                </td>

                <td width = "86%">

                    <%
                    Response.Write(strs[6]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易时间
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[7]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易流水号
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[8]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    交易结果
                </td>

                <td width = "86%">
                    <%
                     Response.Write(strs[9]);
                    %>

                    &nbsp;[1:成功]
                </td>
            </tr>

            <tr>
                <td width = "14%">
                    手续费总额
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[10]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    银行卡类型
                </td>

                <td width = "86%">
                    <%
                     Response.Write(strs[11]);
                    %>

                    &nbsp;[0:借记卡 1：准贷记卡 2:贷记卡]
                </td>
            </tr>

            <tr>
                <td width = "14%">
                    银行备注
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[12]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    错误信息描述
                </td>

                <td width = "86%">
                    <%
                     Response.Write(strs[13]);
                    %>

                </td>
            </tr>
            <tr>
                <td width = "14%">
                    IP
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[14]);
                    %>

                </td>
            </tr>
            <tr>
                <td width = "14%">
                    Referer
                </td>

                <td width = "86%">

                    <%
                        Response.Write(strs[15]);
                    %>

                </td>
            </tr>
             <tr>
                <td width = "14%">
                    商户备注(base64编码的字符串，如需要返回的需要base64解码原文)
                </td>

                <td width = "86%">

                   <%
                    //String merComment = (String)vc.get(16);
					//BASE64Decoder decoder = new BASE64Decoder();
					//new String(decoder.decodeBuffer(merComment));
                    Response.Write(strs[16]);
                    %>


                </td>
            </tr>
        </table>
                    
     <%          
           
       }
    %>
</body>
</html>
