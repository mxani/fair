<?php

namespace App\Magazines;
use App\Model\Post;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;

class Posts extends Magazine{
   
    public function index($type){
    }

    public function show(){
        $post = Post::where('title',$this->update->message->text)->where('status',1)->first();
        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('postMessage',['post'=>$post])->render(),
            'parse_mode'=>'html'
        ]);
        $send();

    }

}
