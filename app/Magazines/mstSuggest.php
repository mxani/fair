<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\answerInlineQuery;
use XB\telegramMethods\editMessageText;
use App\Model\Product;

class mstSuggest extends Magazine{
    public function main(){
        $result=[
            [
                'type'=>'article',
                'id'=>'invate',
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

        $id=$this->update->inline_query->query??1;
        $products=Product::where('id','>=',$id)->take(2)->
        union(Product::where('id','<',$id)->take(1)->orderby('id','desc'))->get();

        foreach($products as $product){
            $keys=[];
            if(count($product->files)>1){
                $keys[]=[
                    [
                        'text'=>'تصویر بعدی',
                        'callback_data'=>interlink(['goto'=>'mstSuggest@productShow','id'=>$product->id,'pic'=>1])
                    ]
                ];
            }
            $keys[]=[
                [
                    'text'=>'بفرمایید',
                    'url'=>'https://t.me/'.config('XBtelegram.bot-username')
                ],
                [
                    'text'=>'پیشنهاد به دوستان',
                    'switch_inline_query'=>$this->update->inline_query->query,
                ]
            ];
            $result[]=[
                'type'=>'article',
                'id'=>'product_'.$product->id,
                'title'=>"🎁  ".($product->category()->first()->name??'')." ➡️ {$product->title}  🎁",
                'input_message_content'=>[
                    'message_text'=>view('productMessage',['product'=>$product,'pic'=>0])->render(),
                    'parse_mode'=>'html'
                ],
                'reply_markup'=>[
                    'inline_keyboard'=>$keys
                ],
                'is_personal'=>true
            ];
        }

        $answer=[
            'inline_query_id'=>$this->update->inline_query->id,
            'results'=>$a=json_encode($result)
        ];

        $answer=new answerInlineQuery($answer);
        $answer();
    }

    public function productShow(){
        $message=['inline_message_id'=>$this->update->callback_query->inline_message_id,'parse_mode'=>'html'];

        ///> validation palce is here. probably don't need to check <///

        $keyBpara=['flow'=>$this->detect->data->id??false];
        $keyBpara['count']=$this->detect->data->count??0;
        $product = Product::find($keyBpara['flow']);

        if(empty($product)){
            ///> answer callback
            return;
        }

        $pic=null;

        if(count($product->files)>1){
            $pic=$this->detect->data->pic??0;
            if($keyBpara['count']<50){
                $keyBpara['count']++;
                $keyBpara['prevpic']=empty($product->files[$pic-1])?null:$pic-1;
                $keyBpara['nextpic']=empty($product->files[$pic+1])?null:$pic+1;
            }
        }


        $message['text']=view('productMessage',['product'=>$product,'pic'=>$pic])->render();
        $message['reply_markup']=view('master.inlineProductKeyboard',$keyBpara)->render();
        $send=new editMessageText($message);
        $send();
    }
    
}
