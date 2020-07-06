<?php


namespace App\WebSocket;

use App\Utility\Traits\ResponseTrait;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

/**
 * Class WebSocketParser
 *
 * 此类是自定义的 websocket 消息解析器
 * 此处使用的设计是使用 json string 作为消息格式
 * 当客户端消息到达服务端时，会调用 decode 方法进行消息解析
 * 会将 websocket 消息 转成具体的 Class -> Action 调用 并且将参数注入
 *
 * @package App\WebSocket
 */
class WebSocketParser implements ParserInterface
{
    use ResponseTrait;

    /**
     * @param $raw 客户端原始消息
     * @param $client
     * @return Caller|null Socket 调用对象
     */
    public function decode($raw, $client): ?Caller
    {
        // 解析 客户端原始消息
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            ServerManager::getInstance()->getSwooleServer()->push($client->getFd(), $this->error('请求包格式错误'));
            return null;
        }

        // new 调用者对象
        $caller = new Caller();
        // 路由到不同控制器/方法
        $class = '\\App\\HttpController\\Websocket\\' . ucfirst($data['class'] ?? 'Pvp');
        $caller->setControllerClass($class);
        // 设置被调用的方法
        $caller->setAction($data['action'] ?? 'index');
        // 检查是否存在args
        if (!empty($data['content'])) {
            // content 无法解析为array 时 返回 content => string 格式
            $args = is_array($data['content']) ? $data['content'] : ['content' => $data['content']];
        }

        // 设置被调用的Args
        $caller->setArgs($args ?? []);
        return $caller;
    }

    /**
     * @param Response $response
     * @param $client
     * @return string|null 发送给客户端的消息
     */
    public function encode(Response $response, $client): ?string
    {
        return $response->getMessage();
    }
}