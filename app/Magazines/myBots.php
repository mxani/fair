<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;
use App\Model\Person;

class myBots extends Magazine{
    public function __construct(&$u,&$s,&$m,&$d){
        parent::__construct($u,$s,$m,$d);
        $this->person=$this->share['person']??Person::where('telegramID',$this->detect->from->id)->first();
        $this->api=sendMessage::class;
        $this->message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        if($this->detect->type=='callback_query'){
            $this->message['message_id']=$this->update->callback_query->message->message_id;
            $this->api=editMessageText::class;
        }
    }

    protected $person,$api,$message;

    public function main(){
        $this->message['text']=view('master.myBotsMessage',
            ['count'=>$this->person->tenants->count()])->render();
        $this->message['reply_markup']=view('master.myBotsKeyboard',
            ['items'=>$this->person->tenants->toArray()])->render();
        $send=new $this->api($this->message); 
        $send();
    }
    
    public function show(){
        if(empty($tenant=$this->getTenant())){
            $this->main();
            return;
        }

        $this->message['text']=view('master.myBotDetailMessage',['tenant'=>$tenant])->render();
        $this->message['reply_markup']=view('master.myBotDetailKeyboard',
            ['tenant'=>$tenant])->render();
        $send=new $this->api($this->message); 
        $send();
    }

    public function del(){
        if(empty($tenant=$this->getTenant())){
            $this->main();
            return;
        }

        $this->message['text']=view('master.deleteMyBotMessage',['tenant'=>$tenant])->render();
        $this->message['reply_markup']=view('master.deleteMyBotKeyboard',
            ['tenant'=>$tenant])->render();
        $send=new $this->api($this->message); 
        $send();
    }

    public function delConfirm(){
        if(empty($tenant=$this->getTenant())){
            $this->main();
            return;
        }
        if(\File::exists('bot/tenants/'.$tenant->token)){
            \File::makeDirectory('bot/tenants/deleted/');
            \File::move('bot/tenants/'.$tenant->token,'bot/tenants/deleted/'.$tenant->token);
        }
        $tenant->delete();
        $answer=new \XB\telegramMethods\answerCallbackQuery([
            'callback_query_id'=>$this->update->callback_query->id,
            'text'=>'حذف شد‼️‼️',
        ]);
        $answer();
        $this->main();
    }





    ///> utils
    protected function getTenant(){
        return $this->person->tenants()->
            find($this->detect->data->tenant??$this->meet['tenant']??null);
    }
    
}
