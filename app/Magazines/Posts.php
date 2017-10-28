<?php

namespace App\Magazines;
use App\Model\Post;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;

class Posts extends Magazine{
    protected $text = '';
    public function blog(){
        $selected_id = false;
        $message=['chat_id'=>$this->detect->from->id,'parse_mode' => 'HTML'];
        $api=sendMessage::class;
        if($this->detect->type == 'callback_query'){
            $api=editMessageText::class;
            $message['message_id'] = $this->update->callback_query->message->message_id;
            $selected_id = $this->detect->data->id??false; 
        }

        $posts = Post::where('type','blog')->where('status', 1)->orderby('id','desc');
        $keyBpara=[];
        if($posts->count() == 1){
            $post = $posts->first();
            $keyBpara['current_id']=$post->id;
        }else{

            if($selected_id){
                $posts = $posts->where('id','<=',$selected_id); 
            }
            $posts = $posts->take(2)->get();

            if(!empty($posts[0])){
                $post = $posts[0];
                $prevPost = Post::where('type','blog')->where('id','>',$posts[0]->id)->first();
                $keyBpara['current_id'] = $post->id;
                $keyBpara['prev'] = $prevPost->id??null;
                if(!empty($keyBpara['prev'])){
                    $keyBpara['prev_title'] = 'قبلی : '.Post::where('id',$prevPost->id)->where('type','blog')->first()->title ;
                }
            }

            if(!empty($posts[1])){
                $keyBpara['next'] = $posts[1]->id;
                $keyBpara['next_title'] = 'بعدی : '.$posts[1]->title;
            }
        }

        echo $message['text'] = $this->text."مطلبی برای نمایش وجود ندارد.";
        if(!empty($post)){
            $message['text'] = view('postMessage',['post'=>$post])->render();
        }
        $message['reply_markup'] = view('postKeyboard',$keyBpara)->render();
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
