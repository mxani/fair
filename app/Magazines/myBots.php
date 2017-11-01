<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;

class myBots extends Magazine{
    public function main(){
        $person=$this->share['person'];
        $items=$person->tenants;
        $para=['count'=>$items->count()];

        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('master.myBotsMessage',$para)->render(),
            'parse_mode'=>'html',
            'reply_markup'=>view('master.myBotsKeyboard',['items'=>$items->toArray()])->render(),
            ]);
        $send();
    }
    
}
