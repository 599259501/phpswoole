<?php
/**
 * Created by PhpStorm.
 * User: 40435
 * Date: 2018/5/16
 * Time: 21:03
 */
require("./constvar.php");
require("./websocket.php");
require("./broadcast.php");
require("./protocl.php");
require("./config.php");
require("./redisSubProcess.php");
@ini_set('default_socket_timeout', -1);
// 创建一个新的进程去监听订阅事件
//$redisSubProcess = new swoole_process('subProcessCallback');
$webSeraver = new WebSocket(8087);
$webSeraver->websocket_server->set(array(
    'worker_num' => 2,
    'task_worker_num'=> 1,
));

$webSeraver->websocket_server->on("start", function($serv){
    /*global $redisSubProcess;
    $redisSubProcess->webSocket = &$serv;
    $redisSubProcess->start();*/
    echo "websocket server starting\r\n";
});

// 设置异步任务
/*$webSeraver->websocket_server->on("task", function($serv, $task_id, $from_id, $data){
    // 集群模式下-所有websocket服务都采用redis订阅模式去监听自消息事件
    echo "task begin...\r\n";
    while(true) {
        global $config;
        $redis = new Redis();
        $redis->pconnect($config['redis']['host'], $config['redis']['port']);
        $redis->subscribe(array(CHANNEL_ZHIBO_MSG),"processChannelMsg");
        sleep(1);
    }
});

$webSeraver->websocket_server->on("finish", function($serv, $task_id, $from_id, $data){
    echo "task finsh\r\n";
});

$webSeraver->onOpen(function(swoole_websocket_server $server,$frame){
    global $webSeraver;
    $conn = new WebSocketConn();
    $conn->fd = $frame->fd;
    $conn->user = "";
    $webSeraver->fdTable[$frame->fd] = $conn;
});

$webSeraver->onMessage(function (swoole_websocket_server $server, $frame){
    // 解析协议
    $protoclObj = json_decode($frame->data);
    if(!$protoclObj){
        return;
    }

    $protoclObj->fd = $frame->fd;

    if (!isset($protoclObj->cmd)){
        // 直接丢包
        return;
    }
    global $webSeraver;
    $webSeraver->dispatchProtocl($protoclObj);
    $server->task("begin",0);
});

$webSeraver->onClose(function ($server, $fd) {
    echo "client {$fd} closed\r\n";
});*/

$webSeraver->websocket_server->start();


