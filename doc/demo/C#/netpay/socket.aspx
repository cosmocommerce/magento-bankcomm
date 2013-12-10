<%@ Page Language="C#" %>
<%@ Import Namespace="System.Net.Sockets" %>

<%
    

    
    TcpClient client = new TcpClient("182.119.117.106", 8080);
    NetworkStream stream = client.GetStream();
    String message = "<?xml version='1.0' encoding='UTF-8'?>";
    message = message + "<Message>";
    message = message + "<TranCode>cb2206_RefundOp</TranCode>";
    message = message + "<MsgContent><BOCOMB2C><opName>cb2206_RefundOp</opName><reqParam><merchantID>301310063009501</merchantID><operator>11</operator><order>22</order><date>20120306</date><amount>145</amount><comment>00</comment></reqParam></BOCOMB2C></MsgContent>";
    message = message + "</Message>";
   
    Byte[] data = System.Text.Encoding.UTF8.GetBytes(message);
    stream.Write(data, 0, data.Length);
    data = new Byte[256];
    String responseData = String.Empty;
    Int32 bytes = stream.Read(data, 0, data.Length);
    responseData = System.Text.Encoding.UTF8.GetString(data, 0, bytes);
    stream.Close();
    client.Close();
    Response.Write(responseData);
    
    
%>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<script runat="server">

</script>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>无标题页</title>
</head>
<body>
    <form id="form1" runat="server">
    <div>
    
    </div>
    </form>
</body>
</html>
