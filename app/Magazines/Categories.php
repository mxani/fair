<?php

namespace App\Magazines;

use XB\theory\Magazine;
use App\Model\Category;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;

class Categories extends Magazine{
    public function index(){
        $categories = Category::get(['name','id'])->toarray();

        $message=[
            'chat_id'=>$this->detect->from->id,
            'text'=>"لطفا یک دسته انتخاب کنید.",
            'parse_mode'=>'html',
            'reply_markup'=>view('categoryKeyboard',['items'=>$categories])->render()
        ];

        $api=sendMessage::class;
        if($this->detect->type=='callback_query'){
            $api=editMessageText::class;
            $message['message_id']=$this->update->callback_query->message->message_id;
        }
        $send=new $api($message);
        $send();
    }
    
}
