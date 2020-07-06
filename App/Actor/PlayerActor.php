<?php


namespace App\Actor;


use App\Utility\Tool\ConnectTableTool;
use App\Utility\Traits\ResponseTrait;
use EasySwoole\Actor\AbstractActor;
use EasySwoole\Actor\ActorConfig;
use EasySwoole\EasySwoole\ServerManager;


/**
 * 玩家Actor
 * Class PlayerActor
 * @package App\Actor
 */
class PlayerActor extends AbstractActor
{
    private $isReboot = 0;

    //机器人只需要记录宿主机actorId 用于转发消息
    private $hostMachineActorId;

    private $room_id;
    private $token;
    //状态 0大厅 1匹配中 2匹配完成 3房间准备中 4游戏中 5游戏结算中
    private $status;

    use ResponseTrait;

    /**
     * 配置当前的Actor
     * @param ActorConfig $actorConfig
     */
    public static function configure(ActorConfig $actorConfig)
    {
        $actorConfig->setActorName('PlayerActor');
    }

    /**
     * Actor首次启动时
     */
    protected function onStart()
    {
        //$this->lastHeartBeat = time();
        $this->isReboot = $this->getArg()['isReboot'];
        if ($this->isReboot == 1) {
            $this->hostMachineActorId = $this->getArg()['hostMachineActorId'];
            $this->room_id = $this->getArg()['room_id'];
        }

        if ($this->isReboot != 1) {
            //更新存储
            $this->token = $this->getArg()['token'];
            ConnectTableTool::getInstance()->exchangeActorId($this->token, $this->actorId());
            ConnectTableTool::getInstance()->getConnectInfo($this->token);
        }
    }

    /**
     * Actor收到消息时
     * @param $msg
     */
    protected function onMessage($msg)
    {
        //$this->lastHeartBeat = time();
        $msg = json_decode($msg, true);
        switch ($msg['command']) {
            case PlayerCommand::startMatch:
//                if ($this->status > 0) {
//                    $this->sendMsgActorId($this->actorId(), $this->success(PlayerCommand::startMatch));
//                    return $this->success();
//                    break;
//                }
                //
                /**
                 * 插入redis队列
                 */

                $this->status = 1;
                $this->sendMsgActorId($this->actorId(), $this->success(PlayerCommand::startMatch));
                break;

            default:
                echo 'command error';
        }
    }

    /**
     * Actor即将退出前
     * @param $arg
     */
    protected function onExit($arg)
    {
        $actorId = $this->actorId();
        echo "Player Actor {$actorId} onExit\n";
    }

    /**
     * Actor发生异常时
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable)
    {
        $actorId = $this->actorId();
        echo "Player Actor {$actorId} onException\n";
    }

    public function sendMsgActorId($actorId, $msg)
    {
        //机器人则消息发送宿主机
        if ($this->isReboot == 1) {
            $actorId = $this->hostMachineActorId;
        }
        $token = ConnectTableTool::getInstance()->getToken($actorId);
        $connectInfo = ConnectTableTool::getInstance()->getConnectInfo($token);
        //指定发送的人
        $msg = is_array($msg) ? $msg : json_decode($msg, true);
        $msg['data']['self'] = $actorId;
        $bool = ServerManager::getInstance()->getSwooleServer()->push($connectInfo->getFd(), json_encode($msg, JSON_UNESCAPED_UNICODE));
    }
}