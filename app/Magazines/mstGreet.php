<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Person;

class mstGreet extends Magazine{
    public function main(){

        $person= new Person();
        $person->telegramID=$this->detect->from->id;
        // dd($person);
        $person->detail=[
            'first_name'=>$this->detect->from->first_name,
            'last_name'=>$this->detect->from->last_name??'-',
            'username'=>$this->detect->from->username??'-',
            'deeplink'=>$this->detect->type=='message' && substr($this->update->message->text,0,6)=='/start'?
                substr($this->update->message->text,7):false,
        ];
        $person->save();

        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('master.welcomeMessage',['person'=>$person])->render(),
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