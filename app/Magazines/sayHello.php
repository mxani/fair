<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;

class sayHello extends Magazine{
    public function main(){

        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('welcomeMessage')->render(),
            'parse_mode'=>'html'
        ]);
        $send();
        $this->mainMenu();

    }

    public function mainMenu(){

        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('defaultMessage')->render(),
            'parse_mode'=>'html',
            'reply_markup'=>view('mainMenu')->render()
        ]);
        $send();
    }

}