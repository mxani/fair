<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\answerInlineQuery;
use App\Model\Product;

class mstSuggest extends Magazine{
    public function main(){
        $result=[
            [
                'type'=>'article',
                'id'=>'10',
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
                            [
                                'text'=>'پیشنهاد به دوستان',
                                'switch_inline_query'=>'',
                            ]

                        ]
                    ]
                ],
                'is_personal'=>true
            ]
        ];

        if(empty($this->update->inline_query->query)){
            $product=Product::first();
        }else{
            $product=Product::find($this->update->inline_query->query);
        }

        if(!empty($product)){
            $result[]=[
                'type'=>'article',
                'id'=>'1',
                'title'=>'🎁  '.$product->title.'  🎁',
                'input_message_content'=>[
                    'message_text'=>view('productMessage',['product'=>$product,'pic'=>0])->render(),
                    'parse_mode'=>'html'
                ],
                'reply_markup'=>[
                    'inline_keyboard'=>[
                        [
                            [
                                'text'=>'بفرمایید',
                                'url'=>'https://t.me/'.config('XBtelegram.bot-username')
                            ],
                            [
                                'text'=>'پیشنهاد به دوستان',
                                'switch_inline_query'=>$this->update->inline_query->query,
                            ]
                        ]
                    ]
                ],
                'is_personal'=>true
            ];
        }

        $answer=[
            'inline_query_id'=>$this->update->inline_query->id,
            'results'=>json_encode($result)
        ];

        $answer=new answerInlineQuery($answer);
        $answer();
    }
    
}
