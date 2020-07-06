<?php

namespace App\Actor;


class PlayerCommand
{
    const startMatch = 1;
    const stopMatch = 2;
    const rankSuccess = 3;
    const ready = 4;
    const sendRandom = 5;
    const exitRoom = 6;
    const exit = 7;
    const killPlayer = 8;
    const beKilled = 9;
    const selectPlayer = 10;
    const attack = 11;


    const sendData = 41;
    const onStartFrameSync = 51;
    const reissueFrame = 61;
    const issueFrame = 71;
    const randomSend = 81;
    const savePlayerInfo = 91;
    const sendMsgToClient = 101;
    const exchangeData = 121;
    const endPlay = 131;

    const createRoom = 141;
    const joinRoom = 151;
    const getPlayerStatus = 161;

    protected $command;
    protected $arg;

    public static $player_status = [
        'ready' => 1,
        'in_room' => 2,
        'playing' => 3
    ];

    public static function send($command, $args = [])
    {
        return json_encode(['command' => $command, 'args' => $args], JSON_UNESCAPED_UNICODE);
    }
}