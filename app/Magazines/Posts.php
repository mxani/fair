<?php

namespace App\Magazines;
use App\Model\Post;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;

class Posts extends Magazine{
   
    public function blog(){
        $selected_id = false;
        $message=['chat_id'=>$this->detect->from->id,'parse_mode' => 'HTML'];
        $api=sendMessage::class;
        if($this->detect->type == 'callback_query'){
            $api=editMessageText::class;
            $message['message_id'] = $this->update->callback_query->message->message_id;
            $selected_id = $this->detect->data->id??false; 
        }

        $posts = Post::where('status', 1)->orderby('id','desc'); 
        if($selected_id){
            $posts = $posts->where('id','<=',$selected_id); 
        }
        $posts = $posts->take(2)->get();

        if(!empty($posts[0])){
            $prevPost = Post::where('id','>',$posts[0]->id)->first();
            $kayBpara['prev'] = $prevPost->id??null;
            $kayBpara['prev_title'] = 'قبلی : '.$posts[0]->title;
        }

        if(!empty($posts[1])){
            $kayBpara['next'] = $posts[1]->id;
            $kayBpara['next_title'] = 'بعدی : '.$posts[1]->title;
        }

        $message['text'] = view('postMessage',['post'=>$posts[0]])->render();
        $message['reply_markup'] = view('postKeyboard',$kayBpara)->render();
        (new $api($message))->call();

    }

    public function showPage(){
        $post =$this->share['post'];
        $send=new sendMessage([
            'chat_id'=>$this->update->message->chat->id,
            'text'=>view('postMessage',['post'=>$post])->render(),
            'parse_mode'=>'html'
        ]);
        $send();
    }

}
