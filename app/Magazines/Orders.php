<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Person;
use App\Model\Product;
use App\Model\Master\Order;
use App\Model\Master\Tenant;

class Orders extends Magazine{
    public function main(){
        $person=Person::where('telegramID',$this->detect->from->id??$this->detect->from->id)->first();
        if(empty($person)){
            $this->caller('mstGreet')->main();
            return;
        }

        $id=$this->meet['order']['product_id']=
            $this->detect->data->id??$this->$this->meet['order']['product_id']??null;
        $product=Product::find($id);
        if(empty($id)|| empty($product)|| !$product->orderable){
            $this->caller(mstGreet::class)->mainMenu();
            return;
        }

        if(empty($person->detail['phone_number'])){
            $this->meet['request']['ref_mag']='Orders';
            $this->meet['request']['ref_car']='main';
            $this->caller(request::class)->contact();
            return;
        }

        $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        $message['text']=view('master.neworderMessage')->render();
        $message['reply_markup']=view('master.defaultMenu')->render();
        $this->meet['goto']='Orders@getToken';
        $send=new sendMessage($message);
        $send();
    }

    public function getToken(){
        $botToken=$this->update->message->text??'';
        if($botToken=='بیخیال شدم'){
            $this->caller(mstGreet::class)->mainMenu();
            return;
        }
        $this->meet['goto']='Orders@getToken';
        if($botToken=='راهنماییم کن'){
            $this->help('token');
            return;
        }

        if(!preg_match("/^\d+:\S+$/", $botToken) || false===$bot=$this->isValidToken($botToken)){
            $this->invalidToken();
            return;
        }

        $product=Product::find($this->meet['order']['product_id']);
        if(empty($product)|| !$product->orderable){
            $this->caller(mstGreet::class)->mainMenu();
            return;
        }


        $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        $message['text']=view('master.greetBotMessage',json_decode($bot,true)['result'])->render();
        $message['reply_markup']=view('master.defaultMenu')->render();
        $this->meet['goto']='mstGreet@mainMenu';
        $send=new sendMessage($message);
        $send();
    }
    







    ///> privates
    private function help($case){
        $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        $message['text']=view('master.getTokenHelpMessage')->render();
        $message['reply_markup']=view('master.defaultMenu')->render();
        $send=new sendMessage($message);
        $send();
    }

    private function invalidToken(){
        $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        $message['text']="توکن وارد شده صحیح نیست.\n دوباره سعی کنید.";
        $message['reply_markup']=view('master.defaultMenu')->render();
        $send=new sendMessage($message);
        $send();
    }

    private function isValidToken($botToken){
        $old=config('XBtelegram.bot-token');
        if($old==$botToken){
            return false;
        }
        config(['XBtelegram.bot-token'=>$botToken]);
        $info=new \XB\telegramMethods\getMe();
        $raw=$info(false,true);
        config(['XBtelegram.bot-token'=>$old]); 
        if($raw=="{}"){
            return false;
        }
        return $raw;     
    }
}
