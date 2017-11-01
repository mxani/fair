<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Person;

class myBots extends Magazine{
    public function main(){
        $person=$this->share['person']??Person::where('telegramID',$this->detect->from->id)->first();
        $items=$person->tenants;
        $para=['count'=>$items->count()];

        $send=new sendMessage([
            'chat_id'=>$this->detect->from->id,
            'text'=>view('master.myBotsMessage',$para)->render(),
            'parse_mode'=>'html',
            'reply_markup'=>view('master.myBotsKeyboard',['items'=>$items->toArray()])->render(),
            ]);
        $send();
    }
    
}
