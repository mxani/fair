<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\answerInlineQuery;

class Suggest extends Magazine{
    public function main(){
        $answer=[
            'inline_query_id'=>$this->update->inline_query->id,
            'results'=>json_encode([
                [
                    'type'=>'article',
                    'id'=>'robot',
                    'title'=>' - 🌺 دعوتنامه 🌺 - ',
                    'input_message_content'=>[
                        'message_text'=>'    برای مشاهده ی محصولات این فروشگاه  شما دعوت شده اید. <a href="https://t.me/'.config('XBtelegram.bot-username').'">&#8205;</a>',
                        'parse_mode'=>'html'
                    ],
                    'reply_markup'=>[
                        'inline_keyboard'=>[
                            [
                                [
                                    'text'=>'بفرمایید',
                                    'url'=>'https://t.me/'.config('XBtelegram.bot-username')
                                ],

                            ]
                        ]
                    ],
                    'is_personal'=>true
                ]
            ])
        ];

        $answer=new answerInlineQuery($answer);
        $answer();
    }
    
}
