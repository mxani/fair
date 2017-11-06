<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Category;
use App\Model\Post;
use App\Model\ProductCategory;
use XB\telegramMethods\editMessageText;
use XB\telegramMethods\deleteMessage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;

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
        if($posts->count() == 1){
            $post = $posts->first();
            $keyBpara['current_id']=$post->id;
        }else{

            if($selected_id){
                $posts = $posts->where('id','<=',$selected_id); 
            }
            $posts = $posts->take(2)->get();
            if(!empty($posts[0])){
                $post=$posts[0];
                $prevPost = Post::where('id','>',$post->id)->where('type',$postType)->first();
                $keyBpara['current_id'] = $post->id;
                $keyBpara['prev'] = $prevPost->id??null;
                
                if(!empty($keyBpara['prev'])){
                    $keyBpara['prev_title'] = 'قبلی : '.Post::where('id',$prevPost->id)->where('type',$postType)->first()->title ;
                }
                
            }

            if(!empty($posts[1])){
                $keyBpara['next'] = $posts[1]->id;
                $keyBpara['next_title'] = 'بعدی : '.$posts[1]->title;
            }
        }

        $content = $postType=='blog' ? 'مطلبی' : 'صفحه ای';
        $msg_text = $this->text."$content برای نمایش وجود ندارد.";
        if(!empty($post)){
            $msg_text =  view('postMessage',['post'=>$post])->render();
        }
        $msg_reply_markup = view('admin.postKeyboard',$keyBpara)->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
    }

    ############# Add a Picture #############
    public function newPic(){
        if (empty($this->meet['section']['state'])) {
            $this->meet['section'] = ['name'=>'newPic','route'=>'adminPosts@storePic','id'=>$this->detect->data->id, 'postType'=>$this->detect->data->postType];
        } else {
            $this->meet['section'] = ['name'=>'newPic','route'=>'adminPosts@storePic','id'=>$this->meet['section']['id'], 'postType'=>$this->meet['section']['postType']];
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
        }else{
            $message['chat_id'] = $this->detect->from->id;
            $message['text'] = "لطفا فقط یک تصویر را با در نظر گرفتن حالت <code>فشرده (compress)</code> ارسال کنید";
            $message['parse_mode'] = 'html';
            (new sendMessage($message))->call();
            return;
        }
        /* elseif (!empty($this->update->message->video)) {
            $file = $this->update->message->video;
            $file_id = $file->file_id;
        } elseif (!empty($this->update->message->document)) {
            $file = $this->update->message->document;
            $file_id = $file->file_id;
        }*/

        if (!empty($file_id)) {
            $newImage = $this->get_url($file_id);

            $post = Post::find($this->meet['section']['id']);
            $post->thumb = $newImage;
            $post->update();
        }

        $this->meet['magazine']['postType']=$this->meet['section']['postType'];
        unset($this->meet['section']);
        $this->index();

    }

    ############# Edit Title #############
    public function editTitle()
    {
        $this->meet['section'] = ['name'=>'editTitle','route'=>'adminPosts@updateTitle','id'=>$this->detect->data->id, 'postType'=>$this->detect->data->postType];

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
        
        $post->title = $this->update->message->text??$post->title;
        $post->update();

        //if (!empty($this->meet['section']['state'] )) {
        //    $this->editContent();
        //} else {
            $this->meet['magazine']['postType']=$this->meet['section']['postType'];
            unset($this->meet['section']);
            $this->index();
        //}
    }

    ############# Edit Description #############
    public function editContent()
    {
        if (empty($this->meet['section']['state'] )) {
            $this->meet['section'] = ['name'=>'editContent','route'=>'adminPosts@updateContent','id'=>$this->detect->data->id, 'postType'=>$this->detect->data->postType];
        } else {
            $this->meet['section'] = ['name'=>'editContent','route'=>'adminPosts@updateContent','id'=>$this->meet['section']['id'], 'postType'=>$this->meet['section']['postType'],'state'=>'create'];
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
        $post->content = $this->update->message->text??$post->content;
        $post->update();

        //if (!empty($this->meet['section']['state'] )) {
        //    $this->newPic();
        //} else {
            $this->meet['magazine']['postType']=$this->meet['section']['postType'];
            unset($this->meet['section']);
            $this->index();
        //}
    }

    ############# Delete Product #############
    public function destroy()
    {
        $post = Post::find($this->detect->data->id);

        if ($post->delete()) {
            $postType = $this->detect->data->postType;
            $content = $postType=='blog' ? 'مطلب' : 'صفحه';
            $this->text =  "$content <code>$post->title</code> با موفقیت حذف شد. \n";
            $this->index();
        }
    }

    ############# Add New Product #############
    public function newPost()
    {
        
        $post = Post::create([
            'title' => 'عنوان',
            'content' => 'توضیحات',
            'type' => $this->detect->data->postType,
            ]);
            
        $this->meet['section'] = ['name'=>'newPost','route'=>'adminPosts@updateTitle','id'=>$post->id, 'postType'=>$this->detect->data->postType,'state'=>'create'];

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
       
        $client = new Client();
        try{
            $response=$client->request(
                'POST', 
                'http://telerobotic.ir/gfftb2017.php', 
                ['form_params' =>['fileUrl'=>$url,'tenantToken'=>$this->detect->tenant]]
            );
            $result = $response->getBody()->getContents();
        } catch (ClientException $e) {
            echo 'ClientException: '.$e->getMessage();
            return false;
        }catch (TransferException $e) {
            echo 'TransferException: '.$e->getMessage();
            return false;
        }catch (\RuntimeException $e) {
            echo 'RuntimeException: '.$e->getMessage();
            return false;
        }catch (\Exception $e) {
            echo 'RuntimeException: '.$e->getMessage();
            return false;
        }

        return $result;
    }

    private function generateRandomString($length=20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
