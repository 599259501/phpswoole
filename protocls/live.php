<?php
/**
 * Created by PhpStorm.
 * User: 40435
 * Date: 2018/5/27
 * Time: 19:45
 */

class liveProtocl{
    public $mType;
    public $liveId;
    public $msgOrg;
    public $msgBody;
    public $webSocket;


    public function __construct($msgOrgStr,&$webSocket)
    {
        $this->msgOrg = $msgOrgStr;
        $this->webSocket = $webSocket;
        $this->unPackMsg();
    }

    // 这里消息分了好几种，目前主要是，直播间聊天&直播间消息通知
    public function processMsg($myFd){
        switch ($this->mType){
            case MTYPE_LIVE_CHAT:
                $this->doNotifyLiveUser();
                break;
            case MTYPE_LIVE_NOTIFY:
                $this->doNotifyAllLiveUser();
                break;
            default:
                // 直接不处理
                return true;
        }
    }

    public function doNotifyLiveUser($myFd,$white = array()){
        $whiteList = array();
        if (!in_array($whiteList)){
            array_push($whiteList, $white);
        } else{
            $whiteList = $white;
        }

        //todo 往消息队列仍消息，通过task下发到直播间的其它用户
        $msg = $this->packMsg(MTYPE_LIVE_CHAT,$this->liveId,$this->msgBody, $myFd, SERVER_ID);
        /*foreach ($this->webSocket->connections as $fd){
            if (in_array($fd,$whiteList )){
                continue;
            }
            $this->webSocket->webSocket->push($fd, $msg);
        }*/
    }

    public function doNotifyAllLiveUser(){

    }

    public function unPackMsg(){
        $msgObj = json_decode($this->msgOrg);
        if ($msgObj){
            $this->mType = $msgObj->mType;
            $this->liveId = $msgObj->liveId;
            $this->msgBody = $this->msg;
            return true;
        }

        return false;
    }

    public function packMsg($mType,$liveId,$msg,$fd,$svrId){
        return json_encode(array(
            "mType"=> $mType,
            "liveId"=> $liveId,
            "msg"=> $msg,
            "fd"=> $fd,
            "svrId"=> $svrId,
        ));
    }
}