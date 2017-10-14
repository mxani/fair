<?php
use XB\theory\Shoot;



if(!empty($this->update->message->text) && !empty($this->update->message->from)){
    
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


// $this->trigger(function(){return true;},'sayHello');
 


$this->trigger(function($u){
    return !empty($u->message->text) && $u->message->text=='get file';
},function(){
    $send=new XB\telegramMethods\sendPhoto([
        'chat_id'=>$this->detect->from->id,
        'photo'=>'/home/xani/Pictures/Man-Brain-415x347.jpg',
    ]);
    $send('photo');
    $m['fileid']=$send->result->photo[0]->file_id;
});

$this->trigger('default',function() {
    $send=new \XB\telegramMethods\sendMessage([
        'chat_id'=>$this->detect->from->id,
        'text'=>"what is your mean?? 🤔\n try again!!",
    ]);
    $send();
});