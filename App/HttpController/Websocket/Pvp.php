<?php


namespace App\HttpController\Websocket;


use App\Actor\PlayerCommand;
use App\Actor\PlayerActor;
use App\Utility\Tool\ConnectTableTool;


class Pvp extends Base
{
    public function getActorId()
    {
        //判断是否为机器人
        $playerId = $this->getArgs('playerId');
        if (!empty($playerId) && $this->getArgs('isReboot') && PlayerActor::client()->exist($playerId)) {
            return $playerId;
        }
        $connect = ConnectTableTool::getInstance()->getConnectInfo($this->getToken());
        if (empty($connect) || empty($connect->getActorId())) {
            $actorId = PlayerActor::client()->create([
                'token' => $this->getToken(),
                'isReboot' => 0
            ]);
            return $actorId;
        }
        return $connect->getActorId();
    }

    public function startMatch()
    {
        PlayerActor::client()->send($this->getActorId(), PlayerCommand::send(PlayerCommand::startMatch));
    }
}