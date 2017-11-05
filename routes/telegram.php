<?php
use XB\theory\Shoot;
if($this->detect->type == "callback_query" && !empty($this->detect->data->goto)){
    Shoot::trigger('default',$this->detect->data->goto);
    return ;
}

if (empty($this->detect->tenant)) {
        require_once('master.php');
        return;
}
DB::purge('mysql');

if ($this->detect->type=='inline_query') {
    $this->trigger('default', 'Suggest');
    return;
}

if ($this->detect->from->id == config('owner_id') && ($this->update->message->text??'') == '*admin' || ($this->update->message->text??'') == '*مدیریت') {
    $this->meet['mod'] = 'admin';// logged in
}

if (($this->meet['mod'] ??'') != 'admin') {
    ## customer

    if ($this->detect->type=='message') { 

        $this->trigger(function () { return $this->update->message->text == '/start'; }, 'sayHello');
        $this->trigger(function () { return $this->update->message->text == 'نمایش محصولات'; }, 'Categories@index');
        $this->trigger(function () { return $this->update->message->text == 'بلاگ'; }, 'Posts@blog');
        $this->trigger(function () {
            $post=App\Model\Post::where('title', $this->update->message->text)->where('type', 'page')->where('status', 1)->first();
            if (!empty($post)) {
                $this->share['post']=$post;
                return true;
            }
        }, 'Posts@showPage');

    }
    $this->trigger('default', 'sayHello@mainMenu');
    return;
}

##### is admin #####
if (!empty($this->meet['section'])) {
        $this->trigger('default', $this->meet['section']['route']);
        return;
}

if (!empty($this->update->message->text)) {
    switch ($this->update->message->text) {
        case 'مدیریت دسته ها': $this->share['route']= 'adminCategories@index'; break;
        case 'مدیریت محصولات': $this->share['route']= 'adminProducts@showCats'; break;
        case 'مدیریت بلاگ': $this->share['route']= 'adminPosts@index'; $this->meet['magazine']=['name'=>'adminPosts','postType'=>'blog']; break;
        case 'مدیریت صفحات': $this->share['route']= 'adminPosts@index'; $this->meet['magazine']=['name'=>'adminPosts','postType'=>'page']; break;
        case 'مدیریت گزارشات': $this->share['route']= 'adminReports@index'; break;
        case 'اطلاع رسانی': $this->share['route']= 'adminNotices@index'; break;
        case 'آموزش کار با پنل': $this->share['route']= 'adminNotices@index'; break;
        case 'خروج از مدیریت': $this->share['route']= 'adminPanel@logout'; break;
        default: $this->share['route']='sayHello@adminMenu'; break;
    }
    $this->trigger('default', $this->share['route']);
}