<?php
/**
 * Created by PhpStorm.
 * User: 40435
 * Date: 2018/5/17
 * Time: 21:51
 */
class Broadcast{
    public function broadcastAllUser($server, $ignoreFd = array()){
        if (!($server instanceof swoole_websocket_server)){
            return false;
        }

        foreach ($server->connections as $fd){
            if (in_array($fd, $ignoreFd)){
                continue;
            }
        }
    }
}