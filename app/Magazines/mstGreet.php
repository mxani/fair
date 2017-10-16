<?php

namespace App\Magazines;

use App\Helpers\Master\Magazine;
use XB\telegramMethods\sendMessage;

class mstGreet extends Magazine{

    protected function index(){
        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('master.welcomeMessage')->render(),
            'parse_mode'=>'html'
        ]);
        $send();
        $this->mainMenu();

    }

    public function mainMenu(){

        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('master.defaultMessage')->render(),
            'parse_mode'=>'html',
            'reply_markup'=>view('master.mainMenu')->render()
        ]);
        $send();
    }
    
}
