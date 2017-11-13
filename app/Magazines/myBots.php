<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;
use App\Model\Person;

class myBots extends Magazine{
    use \App\Helpers\Master\BotToken;

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
        $tenantPath=base_path('bot/tenants/'.$tenant->token);
        if(\File::exists($tenantPath)){
            if(!\File::exists('bot/tenants/deleted/')){
                \File::makeDirectory('bot/tenants/deleted/');
            }
            \File::move($tenantPath,'bot/tenants/deleted/'.$tenant->token);
            $tenant->delete();
        }
        $answer=new \XB\telegramMethods\answerCallbackQuery([
            'callback_query_id'=>$this->update->callback_query->id,
            'text'=>'حذف شد‼️‼️',
        ]);
        $answer();
        $this->main();
    }

    public function token(){
        if(empty($tenant=$this->getTenant())){
            $this->main();
            return;
        }

        $this->meet['goto']='myBots@getToken';
        $this->meet['tenant']=$tenant->id;
        $this->message['text']=view('master.changeTokenMessage')->render();
        $send=new $this->api($this->message); 
        $send();
        $this->message['text']='توکن را وارد کنید.';
        $this->message['reply_markup']=view('master.defaultMenu')->render();
        $send=new sendMessage($this->message);
        $send();
    }

    public function getToken(){
        $botToken=$this->update->message->text??'';
        if($botToken=='بیخیال شدم'){
            $this->caller(mstGreet::class)->mainMenu();
            return;
        }

        if(!preg_match("/\d+:\S+/", $botToken,$botToken) || false===$bot=$this->isValidToken($botToken[0])){
            $this->meet['goto']='myBots@getToken';
            $this->invalidToken();
            return;
        }

        if(empty($tenant=$this->getTenant())){
            $this->main();
            return;
        }

        $bot=json_decode($bot,true)['result'];
        $botToken=$botToken[0];

        $configs=\File::get("bot/tenants/{$tenant->token}/configs.php");
        $configs=str_replace($tenant->bot_token,$botToken,$configs);
        $configs=str_replace($tenant->detail['username'],$bot['username'],$configs);
        $configs=\File::put("bot/tenants/{$tenant->token}/configs.php",$configs);

        $tenant->bot_token=$botToken;
        $tenant->detail=$bot;
        $tenant->save();

        $this->message['text']='توکن جدید اعمال شد.';
        $this->message['reply_markup']=view('master.mainMenu',['customer'=>$this->person])->render();
        $send=new sendMessage($this->message);
        $send();
    }

    public function cancel(){
        if(empty($tenant=$this->getTenant())){
            $this->main();
            return;
        }
        $diff=\Carbon\Carbon::now()->diff($tenant->expires_at);

        $this->message['text']=view('master.cancelMessage',
            ['diff'=>$diff])->render();
        $this->message['reply_markup']=view('master.cancelKeyboard',
            ['diff'=>$diff,'tenant'=>$tenant])->render();
        $send=new $this->api($this->message);
        $send();   
    }

    public function cancelConfirm(){
        if(empty($tenant=$this->getTenant())){
            $this->main();
            return;
        }
        $diff=\Carbon\Carbon::now()->diff($tenant->expires_at);
        if($diff->invert==0 &&( $diff->y>0|| $diff->m>5)){
            $this->message['text']=view('master.cancelConfirmMessage')->render();
            $send=new $this->api($this->message);
            $send(); 

            $this->message['text']=view('master.canceledMessage',
                ['diff'=>$diff,'tenant'=>$tenant,'customer'=>$this->person])->render();
            $this->message['chat_id']='470453293';
            $send=new sendMessage($this->message);
            $send();  
            return;
        }

        $this->main();
    }



    ///> utils
    protected function getTenant(){
        return $this->person->tenants()->
            find($this->detect->data->tenant??$this->meet['tenant']??null);
    }
    
}
