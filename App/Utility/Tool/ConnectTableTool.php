<?php

namespace App\Utility\Tool;

use App\Utility\Tool\Bean\ConnectTableBean;
use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use Swoole\Table;


class ConnectTableTool
{
    use Singleton;

    const ws_table_token = 'ws:table:token';

    const ws_table_fd_actorId = 'ws:table:fd:actorId';

    public function createFdTable($size = 65536)
    {
        TableManager::getInstance()->add(
            self::ws_table_token,
            [
                'actorId' => ['type' => Table::TYPE_STRING, 'size' => 32],
                'fd' => ['type' => Table::TYPE_INT, 'size' => 10],
            ],
            $size
        );
    }

    public function createTokenTable($size = 65536)
    {
        TableManager::getInstance()->add(
            self::ws_table_fd_actorId,
            [
                'token' => ['type' => Table::TYPE_STRING, 'size' => 32],
            ],
            $size
        );
    }

    public function getToken($key): ?string
    {
        return TableManager::getInstance()->get(self::ws_table_fd_actorId)->get($key, 'token');
    }

    public function getConnectInfo($token): ?ConnectTableBean
    {
        $data = TableManager::getInstance()->get(self::ws_table_token)->get($token);
        if (empty($data)) {
            return null;
        }
        return new ConnectTableBean($data);
    }

    public function saveConnectInfo($token, ConnectTableBean $connectTableBean)
    {
        try {
            TableManager::getInstance()->get(self::ws_table_fd_actorId)->set($connectTableBean->getFd(), ['token' => $token]);
            TableManager::getInstance()->get(self::ws_table_fd_actorId)->set($connectTableBean->getActorId(), ['token' => $token]);
            TableManager::getInstance()->get(self::ws_table_token)->set($token, $connectTableBean->toArray());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function exchangeFd($token, $fd)
    {
        try {
            $info = $this->getConnectInfo($token)->toArray();
            if (!empty($info['fd'])) {
                TableManager::getInstance()->get(self::ws_table_fd_actorId)->del($info['fd']);
                TableManager::getInstance()->get(self::ws_table_token)->del($token);
                $info['fd'] = $fd;
                $this->saveConnectInfo($token, new ConnectTableBean($info));
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function exchangeActorId($token, $actorId)
    {
        try {
            $info = $this->getConnectInfo($token)->toArray();
            if (!empty($info['actorId'])) {
                TableManager::getInstance()->get(self::ws_table_fd_actorId)->del($info['actorId']);
                TableManager::getInstance()->get(self::ws_table_token)->del($token);
            }
            $info['actorId'] = $actorId;
            $this->saveConnectInfo($token, new ConnectTableBean($info));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}