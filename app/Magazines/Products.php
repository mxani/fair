<?php

namespace App\Magazines;

use XB\theory\Magazine;
use App\Model\Category;
use App\Model\Product;
use XB\telegramMethods\sendMessage;

class Products extends Magazine{
    public function index(){
        $callback = json_decode($this->update->callback_query->data);
        $category = Category::find($callback->id);

        $product = $category->products()->first();

     
        $replyMarkup = [
            'inline_keyboard' => [
                [
                    [
                        "text"=> "بعدی",
                        "callback_data"=> (int)$callback->id+1         
                    ], 
                    [
                        "text"=> "قبلی",
                        "callback_data"=> (int)$callback->id-1            
                    ]
                ]
            ]
        ];

        $encodedMarkup = json_encode($replyMarkup);


        $send=new sendMessage([
            'chat_id'=>$this->update->callback_query->from->id,
            'text'=>$product->toJson(),
            'parse_mode'=>'html',
            // 'reply_markup'=>$encodedMarkup
            ]);
        $send();
    }
    
}
