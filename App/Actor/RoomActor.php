<?php


namespace App\Actor;


use EasySwoole\Actor\AbstractActor;
use EasySwoole\Actor\ActorConfig;

class RoomActor extends AbstractActor
{
    /**
     * 配置当前的Actor
     * @param ActorConfig $actorConfig
     */
    public static function configure(ActorConfig $actorConfig)
    {
        $actorConfig->setActorName('RoomActor');
        $actorConfig->setWorkerNum(3);
    }

    /**
     * Actor首次启动时
     */
    protected function onStart()
    {
        $actorId = $this->actorId();
        echo "Room Actor {$actorId} onStart\n";
    }

    /**
     * Actor收到消息时
     * @param $msg
     */
    protected function onMessage($msg)
    {
        $actorId = $this->actorId();
        echo "Room Actor {$actorId} onMessage\n";
    }

    /**
     * Actor即将退出前
     * @param $arg
     */
    protected function onExit($arg)
    {
        $actorId = $this->actorId();
        echo "Room Actor {$actorId} onExit\n";
    }

    /**
     * Actor发生异常时
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable)
    {
        $actorId = $this->actorId();
        echo "Room Actor {$actorId} onException\n";
    }
}