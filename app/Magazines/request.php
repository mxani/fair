<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Person;

class request extends Magazine{
    public function contact(){
        $this->meet['goto']='request@contactSave';
        $send=new sendMessage([
            'chat_id'=>$this->detect->chat->id??$this->detect->from->id,
            'text'=>view('master.getPhoneNumberMessage')->render(),
            'parse_mode'=>'html',
            'reply_markup'=>view('master.phoneNumberMenu')->render(),
            ]);
        $send();
    }

    public function invalidContact(){
        $this->meet['goto']='request@contactSave';
        $send=new sendMessage([
            'chat_id'=>$this->detect->chat->id??$this->detect->from->id,
            'text'=>view('master.errorPhoneNumberMessage')->render(),
            'parse_mode'=>'html',
            'reply_markup'=>view('master.phoneNumberMenu')->render(),
            ]);
        $send();
    }


    public function contactSave(){
        $user_id=$this->update->message->contact->user_id??$this->update->message->text??null;
        if($user_id=='انصراف'){
            $this->caller(mstGreet::class)->mainMenu();
            unset($this->meet['request']);        
            return;
        }
        if($user_id!=$this->detect->from->id){
            $this->invalidContact();
            return;
        }
        $person=$this->share['person'];
        $tmp=$person->detail;
        $tmp['phone_number']=$this->update->message->contact->phone_number;
        $person->detail=$tmp;
        $person->type='customer';
        $person->save();
        $mag='App\Magazines\\'.$this->meet['request']['ref_mag'];
        $car=$this->meet['request']['ref_car']??'main';
        unset($this->meet['request']);
        $this->caller($mag)->$car();
    }
    
}
