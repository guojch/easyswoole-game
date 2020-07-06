<?php


namespace App\Utility\Traits;


Trait ResponseTrait
{
    private $successCode = 1;
    private $errorCode = 0;

    public function success($action = '', $data = [], $msg = 'success')
    {
        return json_encode([
            'action' => $action,
            'msg' => $msg,
            'data' => $data,
            'code' => $this->successCode
        ], JSON_UNESCAPED_UNICODE);
    }

    public function error($msg = 'error', $data = [])
    {
        return json_encode([
            'msg' => $msg,
            'data' => $data,
            'code' => $this->errorCode
        ], JSON_UNESCAPED_UNICODE);
    }
}