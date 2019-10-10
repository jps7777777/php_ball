<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-10-10 0010
 * Time: 16:08
 */


$host = "127.0.0.1";
$port = "8080";
$this->master = socket_create(AF_INET6, SOCK_STREAM, SOL_TCP);
socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($this->master, $host, $port);
socket_listen($this->master, self::LISTEN_SOCKET_NUM);

$msg = [
    'type' => $msg_type, // 有普通消息，上下线消息，服务器消息
    'from' => $msg_resource, // 消息来源
    'content' => $msg_content, // 消息内容
    'user_list' => $uname_list, // 便于同步当前在线人数与姓名
];

$write = $except = NULL;
$sockets = array_column($this->sockets, 'resource'); // 获取到全部的 socket 资源
$read_num = socket_select($sockets, $write, $except, NULL);

foreach ($sockets as $socket) {
    // 如果可读的是服务器 socket, 则处理连接逻辑;
    if ($socket == $this->master) {
        socket_accept($this->master);
        // socket_accept() 接受 请求 “正在 listen 的 socket（像我们的服务器 socket ）” 的连接, 并一个客户端 socket, 错误时返回 false;
        self::connect($client);
        continue;
        // 如果可读的是其他已连接 socket ,则读取其数据,并处理应答逻辑
    } else {
        // 函数 socket_recv() 从 socket 中接受长度为 len 字节的数据，并保存在 $buffer 中。
        $bytes = @socket_recv($socket, $buffer, 2048, 0);

        if ($bytes < 9) {
            // 当客户端忽然中断时，服务器会接收到一个 8 字节长度的消息（由于其数据帧机制，8字节的消息我们认为它是客户端异常中断消息），服务器处理下线逻辑，并将其封装为消息广播出去
            $recv_msg = $this->disconnect($socket);
        } else {
            // 如果此客户端还未握手，执行握手逻辑
            if (!$this->sockets[(int)$socket]['handshake']) {
                self::handShake($socket, $buffer);
                continue;
            } else {
                $recv_msg = self::parse($buffer);
            }
        }
        // 广播消息
        $this->broadcast($msg);
    }
}













