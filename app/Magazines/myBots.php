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
        $tenant_id=$this->detect->data->tenant??$this->meet['tenant']??null;
        $tenant=$this->person->tenants()->find($tenant_id);
        if(empty($tenant)){
            $this->main();
            return;
        }

        $this->message['text']=view('master.myBotDetailMessage',['tenant'=>$tenant])->render();
        $this->message['reply_markup']=view('master.myBotDetailKeyboard',
            ['tenant'=>$tenant])->render();
        $send=new $this->api($this->message); 
        $send();
    }
    
}
