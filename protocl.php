<?php
/**
 * Created by PhpStorm.
 * User: 40435
 * Date: 2018/5/17
 * Time: 21:49
 */

class Protocl{
}

class ChannelProtocl{
    public $cmd;
    public $msgBody;
    public $channel;
    public $webSvr;
    public $fd;

    public function __construct($cmd,$msgBody,$channel)
    {
        $this->cmd = $cmd;
        $this->msgBody = $msgBody;
        $this->channel = $channel;
    }

    public function processMsg(){
        switch ($this->cmd){
            case CMD_BROADCAST_CHANNEL_USER: // 这里的用途可能是主播广播/或者某位土豪刷礼物的时候给一个广播弹屏幕
                $this->doBroadcastAllUser();
            case CMD_BROADCAST_CHANNEL_MESSAGE: // 这里的用途是聊天消息给主播间的其他用户
                $this->publishTORedis($this->packTaskMsg());
        }
    }

    public function doBroadcastAllUser(){
    }

    public function doBroadcastOtherUser(){
        foreach ($this->webSvr->connections as $fd){
            if ($fd == $this->fd){
                continue;
            }

            $this->webSvr->push($fd, $this->msgBody);
        }
    }

    public function packTaskMsg(){
        return json_encode(
            array(
                "cmd"=>$this->cmd,
                "channel"=>$this->channel,
                "msg"=>$this->msgBody
            )
        );
    }

    public function publishTORedis($data){
        global $config;
        $redis = new Redis();
        $redis->connect($config['redis']['host'], $config['redis']['port']);
        $redis->publish(CHANNEL_ZHIBO_MSG, $data);
        $redis->close();
    }
}