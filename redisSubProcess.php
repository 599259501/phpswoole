<?php
/**
 * Created by PhpStorm.
 * User: 40435
 * Date: 2018/5/20
 * Time: 21:51
 */
function subProcessCallback(swoole_process $worker){
    global $config;
    $redis = new Redis();
    $redis->pconnect($config['redis']['host'], $config['redis']['port']);

    $worker->redis = $redis;
    $redis->webSocket = $worker->webSocket;
    while(true) {
        echo "start sub\r\n";
        $worker->redis->subscribe(array(CHANNEL_ZHIBO_MSG),"processChannelMsg");
        sleep(1);
    }
}

function processChannelMsg($redis, $chan, $msg){
    echo "has rev,\r\n";
    switch ($chan){
        case CHANNEL_ZHIBO_MSG:
            // todo 分发所有的channel信息
            doBroadcastChannelUser($msg);
        default:
            // 直接忽略处理
    }
}

function doBroadcastChannelUser($msg){
    $msgObj = json_decode($msg, true);
    if (!$msgObj){
        return;
    }

    global $webSeraver;
    foreach($webSeraver->websocket_server->connections as $fd){

    }
}