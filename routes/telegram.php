<?php
use XB\theory\Shoot;

if (empty($this->detect->tenant)) {
        require_once('master.php');
        return;
}

if ($this->detect->type=='message') {
        $this->trigger(function () {
            return $this->update->message->text == '/start';
        }, 'sayHello');


        $this->trigger(function () {
            return $this->update->message->text == 'فروشگاه';
        }, 'Categories@index');

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


$this->trigger('default', 'sayHello@mainMenu');