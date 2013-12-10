using System;
using System.Data;
using System.Configuration;

using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;

using System.Net.Sockets;

/// <summary>
///config 的摘要说明
///配置的系统参数和通讯方法示例
///
/// </summary>
public class config
{
    //商户号
    public static string merchantID = "301310063009501";
    //socket bridge通讯ip
    public static string ip = "127.0.0.1";
    //socket bridge端口
    public static int port = 8080; 

	public config()
	{
		
	}

    //与socket bridge通讯的方法示例
    public string sendAndReceive(string sendMsg)
    {
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
        return responseData;
    }
}
