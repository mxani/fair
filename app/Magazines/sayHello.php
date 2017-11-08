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
        $textMessage = view('defaultMessage')->render();
        $this->showMenu( $textMessage, 'mainMenu');
    }

    public function adminMenu(){
        // $admin = $this->update->message->from->first_name ?? 'کاربر';
        // $message = ['type'=>'text','value'=> $admin.'عزیز، به بخش مدیریت خوش آمدید '];
        $textMessage = 'لطفا یکی از <code>گزینه ها</code> را انتخاب کنید.';
        return $this->showMenu($textMessage,'admin.adminMenu');
    }

    private function showMenu($textMessage, $target_menu)
    {
        $send=new sendMessage([
            'chat_id'=>$this->detect->chat->id??$this->detect->from->id??$this->callback_query->message->chat->id,
            'text'=>$textMessage,
            'parse_mode'=>'html',
            'reply_markup'=>view($target_menu)->render()
        ]);
        $send();
    }

    public function first(){
        $person= new Person();
        $person->telegramID=$this->detect->from->id;
        $person->detail=[
            'owner'=>true,
            'first_name'=>$this->detect->from->first_name,
            'last_name'=>$this->detect->from->last_name??'-',
            'username'=>$this->detect->from->username??'-',
        ];
        $person->save();
        unset($this->meet['section']);
        $this->showMenu(view('admin.welcomeMessage')->render(),'admin.adminMenu');
    }

}
