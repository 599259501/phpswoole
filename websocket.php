<?php
/**
 * Created by PhpStorm.
 * User: 40435
 * Date: 2018/5/17
 * Time: 21:57
 */
require_once("./config/redis.php");
require_once("./protocls/live.php");

class WebSocket{
    public $websocketServer;
    public $broadcast;
    public $redisArr;

    public function __construct($port)
    {
        $this->websocketServer = new swoole_websocket_server("0.0.0.0", $port);
        $this->websocketServer->set(array(
            'worker_num' => 2,
            'task_worker_num'=> 1,
        ));
        // 初始化start方法
        $this->websocketServer->on("start", function($serv){
            // todo
        });
        $this->websocketServer->on("open", function(swoole_websocket_server $server,$frame){
            // 保存用户所在频道的数据
            //TODO 用户身份鉴权
            echo $frame->fd." connect server\r\n";
        });
        $this->websocketServer->on("message",function (swoole_websocket_server $server, $frame){
            $this->dispatchMasg($server,$frame->data,$frame->fd);
        });
        $this->websocketServer->on("close",function ($server, $fd){
            echo "close fd=${fd}...\r\n";
        });
        $this->websocketServer->on("task",function($serv, $task_id, $from_id, $data){
            echo "task begin...\r\n";
            while(true) {
                global $config;
                $redis = new Redis();
                $redis->pconnect($config['redis']['host'], $config['redis']['port']);
                $redis->subscribe(array(CHANNEL_ZHIBO_MSG),"processChannelMsg");
                sleep(1);
            }
        });
        $this->websocketServer->on('WorkerStart', function ($serv, $worker_id){
            global $config;
            if ($worker_id <= $serv->setting["worker_num"] && is_null($serv->redis)){
                $serv->redis = new Redis();
                $serv->redis->pconnect($config['redis']['host'], $config['redis']['port']);
            }
        });
        return $this->websocketServer;
    }

    public function dispatchMasg($server,$msg,$fd){
        $msgCnt = json_decode($msg);
        if(!$msgCnt){
            return;
        }
        $msgCnt->fd = $fd;
        switch ($msgCnt->mType){
            case MTYPE_LIVE_CHAT:
                break;
            case CMD_ENTRY_CHANNEL:
                $this->SetFdChannelRelation($server,$unProcessProtocl);
                break;
        }
    }

    public function SetFdChannelRelation($server,&$unProcessProtocl){
        // todo 清除之前用户与频道的映射关系
        $server->redis->hSet("FdChannelMap", $unProcessProtocl->channel, $unProcessProtocl->fd);
        echo $unProcessProtocl->fd." is setting mapping channel=".$unProcessProtocl->channel."\r\n";
    }

    public function newRedisClient(){
        global $config;
        $redis = new Redis();
        $redis->connect($config['redis']['host'], $config['redis']['port']);
        return $redis;
    }
}

class WebSocketConn{
    public $fd;
    public $user;
    public $channel;
}