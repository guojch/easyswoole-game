<?php


namespace App\Utility\Tool\Bean;


use EasySwoole\Spl\SplBean;

class ConnectTableBean extends SplBean
{
    protected $fd;
    protected $actorId;

    /**
     * @return mixed
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     * @param mixed $fd
     */
    public function setFd($fd): void
    {
        $this->fd = $fd;
    }

    /**
     * @return mixed
     */
    public function getActorId()
    {
        return $this->actorId;
    }

    /**
     * @param mixed $actorId
     */
    public function setActorId($actorId): void
    {
        $this->actorId = $actorId;
    }
}