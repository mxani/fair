<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Person;

class request extends Magazine{
    public function contact(){
        $this->meet['getContact']=1;
        $send=new sendMessage([
            'chat_id'=>$this->detect->chat->id??$this->detect->from->id,
            'text'=>"برای ادامه فرآیند, داشتن شماره تلفن همراه شما الزامی میباشد.\nشماره تلفن شما قبلن ثبت نشده است.\nبا فشوردن دکمه زیر شماره تلفن شما بطور خودکار ثبت میشود.\nاین تضمین به شما داده میشود که شماره تلفن شما مورد هیچگونه سؤ استفاده قرار نگیرد",
            'parse_mode'=>'html',
            'reply_markup'=>'{"keyboard":[[{"text":"دراختیار قرار دادن شماره تلفن","request_contact":true},{"text":"انصراف"}]],"resize_keyboard":true,"one_time_keyboard":true}'
            ]);
        $send();
    }

    public function contactSave(){
        if($this->update->message->contact->user_id!=$this->detect->from->id){
            $this->contact();
            return;
        }
        unset($this->meet['getContact']);
        $person=Person::where('telegramID',$this->detect->from->id)->first();
        $tmp=$person->detail;
        $tmp['phone_number']=$this->update->message->contact->phone_number;
        $person->detail=$tmp;
        $person->save();
        $mag='App\Magazines\\'.$this->meet['ref_mag'];
        $car=$this->meet['ref_car']??'main';
        $this->caller($mag)->$car();
    }
    
}
