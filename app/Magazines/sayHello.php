<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;

class sayHello extends Magazine{
    public function main(){

        $replyMarkup = array(
            'keyboard' => array(
                array(
                    ['text'=>'بلاگ'],
                    ['text'=>'فروشگاه']
                ),
                array(
                    ['text'=>'درباره ما'],
                    ['text'=>'تماس با ما']
                )
            ),
            'resize_keyboard'=> true,
            'one_time_keyboard'=> true,
        );
        $encodedMarkup = json_encode($replyMarkup);

        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>"===========\nHELLO. 🤝\n===========",
            'parse_mode'=>'html',
            'reply_markup'=>$encodedMarkup
        ]);
        $send();
    }
    

}