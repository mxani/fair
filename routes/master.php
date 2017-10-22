<?php

// $this->trigger('default','test');
// return;

if($this->detect->type=='inline_query'){
    $this->trigger('default','mstSuggest');
    return;
}

$person=\App\Model\Person::where('telegramID',$this->detect->from->id)->first();

if(empty($person)){
    $this->trigger('default','mstGreet');
    return;
}

$this->share['person']=$person;
if($person->type=='tenant'){

    return;
}elseif($person->type=='personnel'){

    return;
}

if(!empty($this->update->message->text)){
    $this->trigger(function () {
        return $this->update->message->text == 'محصولات';
    }, 'mstCategories');

    $this->trigger(function () {
        return $this->update->message->text == 'بلاگ';
    }, 'Posts@blog');
   
    $this->trigger(function () {
            $post=App\Model\Post::where('title', $this->update->message->text)->where('type','page')->where('status', 1)->first();
            if (!empty($post)) {
                    $this->share['post']=$post;
                    return true;
            }
    }, 'Posts@showPage');
}

if(!empty($this->meet['getContact'])){
    if($this->detect->msgtype=='contact'){
        $this->trigger('default','request@contactSave');
        return;
    }
    if($this->detect->msgtype=='text'&&$this->update->message->text == 'انصراف'){
        $this->trigger('default','mstGreet@reset');
        return;
    }
    $this->trigger('default','request@contact');
    return;
}

$this->trigger('default','mstGreet@mainMenu');