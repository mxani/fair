<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;

class test extends Magazine{
    public function main(){
        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>"===========\nHELLO11. 🤝\n===========",
            'parse_mode'=>'html',
            'reply_markup'=>'{"keyboard":[[{"text":"U","request_contact":true}]],"one_time_keyboard":true}'
            ]);
        $send();
    }
    
}
