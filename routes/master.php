<?php

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

$this->trigger('default','mstGreet@mainMenu');