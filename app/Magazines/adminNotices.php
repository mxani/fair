<?php

namespace App\Magazines;

use App\Model\Person;
use App\Model\Notice;
use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;
use XB\telegramMethods\deleteMessage;

class adminNotices extends Magazine
{
    public function index()
    {
        $msg_text =  'لطفا یک پیام اطلاع رسانی ایجاد کنید.';
        $msg_reply_markup = view('admin.noticeKeyboard')->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
    }

    ############# Add New Product #############

    public function addNotice()
    {
        $last=Notice::orderby('sent_at','desc')->first()->sent_at??\Carbon\Carbon::now()->subdays(4);
        $diff=\Carbon\Carbon::now()->diff($last->adddays(3));
        if($diff->invert==0 ){
            $message['chat_id'] = $this->detect->from->id;
            $message['text'] = view('admin.noticeLimitMessage', ['diff'=>$diff])->render();
            $message['parse_mode'] = 'html';
            (new sendMessage($message))->call();
            $this->caller(sayHello::class)->adminMenu();
            return;
        }

        $notice = Notice::create(['text' => null]);
        $this->meet['section'] = ['name'=>'newNotice','route'=>'adminNotices@updateNotice','id'=>$notice->id];

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "پیام جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] =  view('cancleMenu')->render();
        $this->meet['cancel']='adminNotices@index';
        (new sendMessage($message))->call();
    }

    public function updateNotice()
    {
        $notice = Notice::find($this->meet['section']['id']);
     
        $notice->text = $this->update->message->text??$notice->text;
        $notice->update();

        $keyBpara['id'] = $notice->id;

        $msg_text =  $notice->text;
        $msg_reply_markup = view('admin.noticeKeyboard',$keyBpara)->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
       
    }

    public function deleteNotice()
    {
        $notice = Notice::find($this->meet['section']['id']);
        $notice->delete();
        unset($this->meet['section']);
        $this->index();

    }
    public function sendNotice(){
        $notice = Notice::find($this->meet['section']['id']);
        $people = Person::get(['telegramID']);
        
        foreach ($people as $person) {
            $send=new sendMessage([
                'chat_id' => $person->telegramID,
                'text' => $notice->text,
                'parse_mode' => 'html',
                ]);
            $send();
        }

        unset($this->meet['section']);
        $send=new sendMessage([
            'chat_id' => $this->detect->from->id,
            'text' => "پیام اطلاع رسانی با موفقیت ارسال شد.",
            'parse_mode' => 'html',
            ]);
        $send();
        $notice->sent_at=\Carbon\Carbon::now();
        $notice->update();
        $this->caller(sayHello::class)->adminMenu();
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
        
    }
}
