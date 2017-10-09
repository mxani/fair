<?php

namespace App\Magazines;

use XB\theory\Magazine;
use App\Model\Category;
use XB\telegramMethods\sendMessage;

class Categories extends Magazine{
    public function index(){
        $categories = Category::get(['id','name']);

        $cat_names = [];

        foreach($categories->toArray() as $category){
           $callback = [
                "id"=>(string)$category['id'],
                "class"=>"Products",
                "method"=>"index"
            ]; 
           array_push($cat_names,[['text'=>$category['name'],"callback_data"=> json_encode($callback)]]);
        }

        
        $replyMarkup000000 = [
            'inline_keyboard' => [
                [
                    [
                        "text"=> "A",
                        "callback_data"=> "A1"            
                    ], 
                    [
                        "text"=> "B",
                        "callback_data"=> "C1"            
                    ]
                ]
            ]
        ];
        

        $replyMarkup = [
            'inline_keyboard' => 
                $cat_names
            
        ];
        $encodedMarkup = json_encode($replyMarkup);

//  dd($encodedMarkup);
        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>"لطفا یک دسته انتخاب کنید.",
            'parse_mode'=>'html',
            'reply_markup'=>$encodedMarkup
            ]);
        $send();
    }
    
}
