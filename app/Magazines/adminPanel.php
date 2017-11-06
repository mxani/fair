<?php

namespace App\Magazines;

use XB\theory\Magazine;
use App\Model\Category;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;

class adminPanel extends Magazine
{  
    public function logout()
    {
        $this->meet['mod']='';
        $this->caller(sayHello::class)->mainMenu();
    }

    public function guide(){
        $send=new sendMessage([
            'chat_id' => $this->detect->from->id,
            'text' => view('guideMessage')->render(),
            'parse_mode' => 'html',
            ]);
        $send();
    }
}
