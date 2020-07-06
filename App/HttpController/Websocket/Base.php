<?php


namespace App\HttpController\Websocket;

use App\Utility\Tool\ConnectTableTool;
use EasySwoole\Socket\AbstractInterface\Controller;

class Base extends Controller
{

    public function getArgs($key = '')
    {
        $args = $this->caller()->getArgs();
        if (empty($key)) {
            return $args;
        } else {
            return $args[$key] ?? '';
        }
    }

    public function getFd()
    {
        return $this->caller()->getClient()->getFd();
    }

    public function getToken()
    {
        return ConnectTableTool::getInstance()->getToken($this->getFd());
    }
}