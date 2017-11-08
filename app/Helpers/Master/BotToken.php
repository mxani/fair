<?php

namespace App\Helpers\Master;

use XB\telegramMethods\sendMessage;

trait BotToken {
    private function isValidToken($botToken){
        $old=config('XBtelegram.bot-token');
        if($old==$botToken){
            return false;
        }
        config(['XBtelegram.bot-token'=>$botToken]);
        $info=new \XB\telegramMethods\getMe();
        $raw=$info(false,true);
        config(['XBtelegram.bot-token'=>$old]); 
        if($raw=="{}"){
            return false;
        }
        return $raw;     
    }

    private function invalidToken(){
        $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        $message['text']=view('master.invalidTokenMessage')->render();
        $message['reply_markup']=view('master.defaultMenu')->render();
        $send=new sendMessage($message);
        $send();
    }
    
}