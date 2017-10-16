<?php

namespace App\Helpers\Master;

use XB\theory\Magazine as Mag;

class Magazine extends Mag{

    public function __call($method,$arguments){
        if(1){
            return call_user_func_array(array($this,$method),$arguments);
        }
        $api=new \XB\telegramMethods\sendMessage([
            'chat_id'=>$this->detect->from->id,
            'text'=>'شما مجوز لازم برای این عمل را ندارید.'
        ]);
        $api();
        die();
    }

}