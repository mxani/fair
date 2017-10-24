<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Category;
use App\Model\Post;
use App\Model\ProductCategory;
use XB\telegramMethods\editMessageText;
use XB\telegramMethods\deleteMessage;

class adminPosts extends Magazine
{

    protected $text = '';

    ############# Display Posts #############
    public function index(){ 

        $postType = $this->detect->data->postType??$this->meet['magazine']['postType']; 

        if (!empty($this->detect->data->id)) {
            $selected_id = $this->detect->data->id;
        }  elseif (!empty($this->meet['section']['id'])) {
            $selected_id = $this->meet['section']['id'];
        } else {
            $selected_id = false;
        }

        $keyBpara['goto'] = 'adminPosts@index';
        $keyBpara['postType'] = $postType;

        $posts = Post::where('type',$postType)->where('status', 1)->orderby('id','desc'); 
        if($selected_id){
            $posts = $posts->where('id','<=',$selected_id); 
        }
        $posts = $posts->take(2)->get();
        if(!empty($posts[0])){
            $prevPost = Post::where('id','>',$posts[0]->id)->where('type',$postType)->first();
            $keyBpara['current_id'] = $posts[0]->id;
            $keyBpara['prev'] = $prevPost->id??null;
            
            if(!empty($keyBpara['prev'])){
                $keyBpara['prev_title'] = 'قبلی : '.Post::where('id',$prevPost->id)->where('type',$postType)->first()->title ;
            }
            
        }

        if(!empty($posts[1])){
            $keyBpara['next'] = $posts[1]->id;
            $keyBpara['next_title'] = 'بعدی : '.$posts[1]->title;
        }

        $msg_text =  view('postMessage',['post'=>$posts[0]])->render();
        $msg_reply_markup = view('admin.postKeyboard',$keyBpara)->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
    }

    ############# Add a Picture #############
    public function newPic(){
        if (empty($this->meet['section']['state'])) {
            $this->meet['section'] = ['name'=>'newPic','id'=>$this->detect->data->id, 'postType'=>$this->detect->data->postType];
        } else {
            $this->meet['section'] = ['name'=>'newPic','id'=>$this->meet['section']['id'], 'postType'=>$this->meet['section']['postType']];
        }

        if (!empty($this->meet['section']['id'])) {
            $id = $this->meet['section']['id'];
        } else {
            $id = $this->detect->data->id;
        }
        $post = Post::find($id);

        $message['chat_id'] = $this->detect->from->id;
        $message['text'] = "یک تصویر جدید برای <code>$post->title</code> ارسال کنید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }
    public function storePic()
    {
        if (!empty($this->update->message->photo)) {
            $file = $this->update->message->photo;
            $file = $file[count($file)-1];
            $file_id = $file->file_id;
        } elseif (!empty($this->update->message->video)) {
            $file = $this->update->message->video;
            $file_id = $file->file_id;
        } elseif (!empty($this->update->message->document)) {
            $file = $this->update->message->document;
            $file_id = $file->file_id;
        }

        //$url = $this->get_url();

        // generate fake image url
        $faker = \Faker\Factory::create('fa_IR');
        $fake_image = $faker->imageurl;
        // add fake_image to product files
        $post = Post::find($this->meet['section']['id']);
        $post->thumb = $fake_image;
        $post->update();

        $this->meet['magazine']['postType']=$this->meet['section']['postType'];
        unset($this->meet['section']);
        $this->index();

    }

    ############# Edit Title #############
    public function editTitle()
    {
        $this->meet['section'] = ['name'=>'editTitle','id'=>$this->detect->data->id, 'postType'=>$this->detect->data->postType];

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "عنوان جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }
    public function updateTitle()
    {
        $post = Post::find($this->meet['section']['id']);
        
        $post->title = $this->update->message->text;
        $post->update();

        if (!empty($this->meet['section']['state'] )) {
            $this->editContent();
        } else {
            $this->meet['magazine']['postType']=$this->meet['section']['postType'];
            unset($this->meet['section']);
            $this->index();
        }
    }

    ############# Edit Description #############
    public function editContent()
    {
        if (empty($this->meet['section']['state'] )) {
            $this->meet['section'] = ['name'=>'editContent','id'=>$this->detect->data->id, 'postType'=>$this->detect->data->postType];
        } else {
            $this->meet['section'] = ['name'=>'editContent','id'=>$this->meet['section']['id'], 'postType'=>$this->meet['section']['postType'],'state'=>'create'];
        }
        
        $chat_id = $this->detect->from->id;
        if ($this->detect->type == 'callback_query') {
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }
        
        $message['chat_id'] = $chat_id;
        $message['text'] = "توضیحات جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }
    public function updateContent()
    {
        $post = Post::find($this->meet['section']['id']);
        $post->content = $this->update->message->text;
        $post->update();

        if (!empty($this->meet['section']['state'] )) {
            $this->newPic();
        } else {
            $this->meet['magazine']['postType']=$this->meet['section']['postType'];
            unset($this->meet['section']);
            $this->index();
        }
    }

    ############# Delete Product #############
    public function destroy()
    {
        $post = Post::find($this->detect->data->id);

        if ($post->delete()) {
            $this->text =  "محصول <code>".$post->title."</code> با موفقیت حذف شد. \n";
            $postType = $this->detect->data->postType;
            $this->index();
        }
    }

    ############# Add New Product #############
    public function newPost()
    {
        
        $post = Post::create([
            'title' => 'عنوان مطلب',
            'content' => 'توضیحات مطلب',
            'type' => $this->detect->data->postType,
            ]);
            
        $this->meet['section'] = ['name'=>'newPost','id'=>$post->id, 'postType'=>$this->detect->data->postType,'state'=>'create'];

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "عنوان جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }


    ## send or edit message ##
    private function my_sendMessage($text, $reply_markup = null)
    {
        $message=['chat_id'=>$this->detect->from->id,'parse_mode' => 'HTML'];
        $api=sendMessage::class;
        if ($this->detect->type == 'callback_query') {
            $api=editMessageText::class;
            $message['message_id'] = $this->update->callback_query->message->message_id;
        }

        $message['text'] = $text;
        if ($reply_markup!=null) {
            $message['reply_markup'] = $reply_markup;
        }
        (new $api($message))->call();
        
        $this->caller(sayHello::class)->adminMenu();
    }

    ## getting the url of uploaded image in telegram ##
    private function get_url($file_id)
    {
        $get =  new \XB\telegramMethods\getFile(['file_id'=>$file_id]);
        $get();
        if (!empty($path = $get->result->file_path)) {
            $url="https://api.telegram.org/file/bot".config('XBtelegram.bot-token')."/$path";
        }
        
        return $url;
    }
}
