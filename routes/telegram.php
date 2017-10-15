<?php
use XB\theory\Shoot;



if($this->detect->type=='message'){
    
        $this->trigger(function(){
        return $this->update->message->text == '/start';
       },'sayHello');


        $this->trigger(function(){
        return $this->update->message->text == 'فروشگاه';
       },'Categories@index');

        $this->trigger(function(){
        return $this->update->message->text == 'بلاگ';
       },'Blog');
    
       $this->trigger(function(){
        return $this->update->message->text == 'تماس با ما';
       },'Blog');   

       $this->trigger(function(){
        return $this->update->message->text == 'درباره ما';
       },'Blog');
}


$this->trigger('default','sayHello@mainMenu');