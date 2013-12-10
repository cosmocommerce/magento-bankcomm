<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>
<%@ Import Namespace="System.Xml"  %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml" >
<head id="Head1" runat="server">
    <title>�̻�֪ͨ</title>
</head>
<body>
   <%
      
           string tranCode = "cb2200_verify";
           string notifyMsg = Request.Params.Get("notifyMsg");
            
           StringBuilder sendMsg = new StringBuilder("");
           //sendMsg.Append("<?xml version='1.0' encoding='UTF-8'?>")
           //��֯���뱨��
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

           //�������ر���
           XmlDocument xmlDoc = new XmlDocument();
           xmlDoc.LoadXml(responseData);
           XmlNodeList list = xmlDoc.GetElementsByTagName("retCode");
           string retCode = list.Item(0).InnerText.Trim();
           list = xmlDoc.GetElementsByTagName("errMsg");
           string errMsg = list.Item(0).InnerText.Trim();
                     
           if(!retCode.Equals("0"))
           {
               Response.Write("���׷����룺" + retCode + "<br>");
               Response.Write("���״�����Ϣ��" + errMsg + "<br>");
           }                  
           else
           {
               string[] strs = notifyMsg.Split('|');
       %>
              <table width = "75%" border = "0" cellspacing = "0" cellpadding = "0">
            <tr>
                <td width = "14%">
                    �̻��ͻ���
                </td>

                <td width = "86%">

                    <%
                    Response.Write(strs[0]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    Э�������
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[1]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    Э���
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[2]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ��������
                </td>

                <td width = "86%">

                    <%
                    Response.Write(strs[3]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ������
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[4]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ����
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[5]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    �ֻ���
                </td>

                <td width = "86%">

                    <%
                    Response.Write(strs[6]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    �̻���ע
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[7]);
                    %>

                </td>
            </tr>

            <tr>
                <td width = "14%">
                    ���б�ע
                </td>

                <td width = "86%">

                    <%
                     Response.Write(strs[8]);
                    %>

                </td>
            </tr>

           
        </table>
                    
     <%          
           
       }
    %>
</body>
</html>
