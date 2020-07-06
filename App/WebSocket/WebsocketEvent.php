<?php


namespace App\WebSocket;


use App\Utility\Tool\Bean\ConnectTableBean;
use App\Utility\Tool\ConnectTableTool;
use EasySwoole\EasySwoole\ServerManager;
use App\Utility\Traits\ResponseTrait;
use EasySwoole\Socket\Dispatcher;

class WebsocketEvent
{
    use ResponseTrait;

    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $req)
    {
        try {
            // 测试
            $token = $req->get['token'];
            if (!$token) {
                ServerManager::getInstance()->getSwooleServer()->push($req->fd, $this->error('Token无效'));
                ServerManager::getInstance()->getSwooleServer()->close($req->fd);
                return true;
            }
            if ($info = ConnectTableTool::getInstance()->getConnectInfo($token) && isset($info['fd'])) {
                ConnectTableTool::getInstance()->exchangeFd($token, $req->fd);
            } else {
                $data = new ConnectTableBean(['fd' => $req->fd]);
                ConnectTableTool::getInstance()->saveConnectInfo($token, $data);
            }
        } catch (\Exception $exception) {
            ServerManager::getInstance()->getSwooleServer()->push($req->fd, $this->error('服务器内部错误'));
            throw new \Exception($exception->getMessage());
        }
    }

    public function onClose(\swoole_server $server, int $fd, int $reactorId)
    {
        $info = $server->getClientInfo($fd);
        /**
         * 判断此fd 是否是一个有效的 websocket 连接
         */
        if ($info && $info['websocket_status'] === WEBSOCKET_STATUS_FRAME) {
            /**
             * 判断连接是否是 server 主动关闭
             */
            if ($reactorId < 0) {
                echo "server close \n";
            }
        }
    }

    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        // 创建一个 Dispatcher 配置
        $conf = new \EasySwoole\Socket\Config();
        // 设置 Dispatcher 为 WebSocket 模式
        $conf->setType(\EasySwoole\Socket\Config::WEB_SOCKET);
        // 设置解析器对象
        $conf->setParser(new WebSocketParser());
        // 创建 Dispatcher 对象 并注入 config 对象
        $dispatch = new Dispatcher($conf);
        $dispatch->dispatch($server, $frame->data, $frame);
    }
}