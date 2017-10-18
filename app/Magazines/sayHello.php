<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Person;

class sayHello extends Magazine
{
    public function main()
    {
        $person= new Person();
        $person->telegramID=$this->detect->from->id;
        $person->detail=[
            'first_name'=>$this->detect->from->first_name,
            'last_name'=>$this->detect->from->last_name??'-',
            'username'=>$this->detect->from->username??'-',
        ];
        $person->save();

        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('welcomeMessage')->render(),
            'parse_mode'=>'html'
        ]);
        $send();
        $this->mainMenu();

    }

    public function mainMenu(){
        $message = ['type'=>'view','value'=>'defaultMessage'];
        $this->showMenu( $message, 'mainMenu');
    }

    public function adminMenu(){
        $message = ['type'=>'text','value'=>'welcome to admin panel'];
        $this->showMenu($message,'admin.adminMenu');
    }

    private function showMenu($textMessage, $target_menu)
    {
        if ($textMessage['type']=='text') {
            $text = $textMessage['value'];
        } elseif ($textMessage['type']=='view') {
            $text = view($textMessage['value'])->render();
        }

        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>$text,
            'parse_mode'=>'html',
            'reply_markup'=>view($target_menu)->render()
        ]);
        $send();
    }
}
