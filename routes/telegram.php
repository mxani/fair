<?php
use XB\theory\Shoot;

$this->trigger(function(){
    return $this->update->message->text=='hi test';
},function(){
    $send=new XB\telegramMethods\sendMessage([
        'chat_id'=>$this->update->message->chat->id,
        'text'=>'<b>test success.</b>',
        'parse_mode'=>'html',
    ]);
    $send() or print 'error:'.$send->getError();
});

$this->trigger(function(){return true;},'sayHello');

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