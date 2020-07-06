<?php

namespace EasySwoole\EasySwoole;


use App\Actor\RoomActor;
use App\Utility\Tool\ConnectTableTool;
use App\WebSocket\WebsocketEvent;
use App\Actor\PlayerActor;
use EasySwoole\Actor\Actor;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Redis;


class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        /**
         * **************** MySQL ORM的连接注册 **********************
         */
        $configData = Config::getInstance()->getConf('MYSQL');
        $config = new \EasySwoole\ORM\Db\Config($configData);
        DbManager::getInstance()->addConnection(new Connection($config));
        /**
         * **************** REDIS 协程连接池 **********************
         */
        $redisConfig = Config::getInstance()->getConf('redis');
        Redis::getInstance()->register('redis', new RedisConfig($redisConfig));
    }

    public static function mainServerCreate(EventRegister $register)
    {
        /**
         * **************** 开启内存表(仅用来映射token与fd关系) **********************
         */
        ConnectTableTool::getInstance()->createFdTable();
        ConnectTableTool::getInstance()->createTokenTable();
        /**
         * **************** websocket控制器 **********************
         */
        $websocketEvent = new WebsocketEvent();
        $register->set(EventRegister::onOpen, function (\swoole_websocket_server $server, \swoole_http_request $req) use ($websocketEvent) {
            $websocketEvent->onOpen($server, $req);
        });
        $register->set(EventRegister::onClose, function (\swoole_server $server, int $fd, int $reactorId) use ($websocketEvent) {
            $websocketEvent->onClose($server, $fd, $reactorId);
        });
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($websocketEvent) {
            $websocketEvent->onMessage($server, $frame);
        });

        /**
         * **************** 注册Actor管理器 **********************
         */
        Actor::getInstance()->setProxyNum(10)->register(PlayerActor::class);
        Actor::getInstance()->setProxyNum(2)->register(RoomActor::class);

        $server = ServerManager::getInstance()->getSwooleServer();
        Actor::getInstance()->setTempDir(EASYSWOOLE_TEMP_DIR)->attachServer($server);
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}