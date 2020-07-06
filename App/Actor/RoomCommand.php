<?php

namespace App\Actor;


class RoomCommand
{


    const joinRoom = 1;
    const startRank =2;
    const sendData = 3;
    const endPlay = 4;



    const ready = 5; //准备
    const killPlayer = 6; //击杀玩家
    const beKilled = 7; //被击杀
    const selectPlayer = 8; //被击杀
    const attack = 9; //被击杀


    protected $command;
    protected $arg;

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command): void
    {
        $this->command = $command;
    }

    /**
     * @return mixed
     */
    public function getArg()
    {
        return $this->arg;
    }

    /**
     * @param mixed $arg
     */
    public function setArg($arg): void
    {
        $this->arg = $arg;
    }


}