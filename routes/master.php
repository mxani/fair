<?php

if($this->detect->type == "callback_query" && !empty($this->detect->data->goto)){
    $this->trigger('default',$this->detect->data->goto);
    return ;
}

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
$this->trigger('default','mstGreet@mainMenu');

if(!empty($this->meet['goto'])){
    $goto=$this->meet['goto'];
    unset($this->meet['goto']);
    $this->trigger('default',$goto);
    return;
}

if($person->type=='customer'){
    $binds=[
        'محصولات'=>'mstCategories',
        'بلاگ'=>'Posts@blog',
        'روباتهای من'=>'myBots',
    ];

    $text=$this->update->message->text??null;
    $post=App\Model\Post::where('title', $text)->where('type','page')->where('status', 1)->first();
    if (!empty($post)) {
            $this->share['post']=$post;
            $binds[$post->title]='Posts@showPage';
    }

    $this->trigger('default',$binds[$text]??'mstGreet@mainMenu');
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
