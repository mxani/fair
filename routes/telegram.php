<?php
use XB\theory\Shoot;

if (empty($this->detect->tenant)) {
        require_once('master.php');
        return;
}

if($this->detect->type=='inline_query'){
    $this->trigger('default','Suggest');
    return;
}

if (empty($this->meet['mod']) || $this->meet['mod'] != 'admin') {
    ## customer

    if ($this->detect->type=='message') {
        $this->trigger(function () {
                return $this->update->message->text == '/start';
        }, 'sayHello');


        $this->trigger(function () {
            return $this->update->message->text == 'نمایش محصولات';
        }, 'Categories@index');

        $this->trigger(function () {
            return $this->update->message->text == 'بلاگ';
        }, 'Posts@blog');

        $this->trigger(function () {
            $post=App\Model\Post::where('title', $this->update->message->text)->where('type', 'page')->where('status', 1)->first();
            if (!empty($post)) {
                $this->share['post']=$post;
                return true;
            }
        }, 'Posts@showPage');
    }
}

if ($this->detect->type=='message' && $this->detect->from->id == config('owner_id')) {
    if ($this->update->message->text == '*admin' || $this->update->message->text == '*مدیریت') {
        $this->meet['mod'] = 'admin';
        // log in
        // show admin menu
        $this->trigger(function () { return true;}, 'sayHello@adminMenu');
    }
                
    if ($this->update->message->text == '*exit' || $this->update->message->text == '*خروج') {
        $this->meet['mod']='';
        // log out
    }
}

$this->trigger('default', 'sayHello@mainMenu');