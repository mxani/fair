<?php
use XB\theory\Shoot;

if (empty($this->detect->tenant)) {
        require_once('master.php');
        return;
}
DB::purge('mysql');

if ($this->detect->type=='inline_query') {
    $this->trigger('default', 'Suggest');
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
    
    if (!empty($this->update->message->text) && ($this->update->message->text == '*admin' || $this->update->message->text == '*مدیریت')) {
        $this->meet['mod'] = 'admin';// logged in
    }
    
    if ($this->meet['mod']??'' == 'admin') {// is admin

        if (!empty($this->meet['section'])){
            $section = $this->meet['section']['name'];
            $magazine = $this->meet['magazine']['name'] ?? 'adminProducts';

            if(!empty($this->update->message->text) && $this->update->message->text){             
                switch( $section ){
                    //category
                    case 'editCat': $this->share['route']= 'adminCategories@update'; break;
                    case 'newCat': $this->share['route']= 'adminCategories@store'; break;
                    //for product & post
                    case 'editTitle': $this->share['route']= $magazine.'@updateTitle'; break;
                    case 'editContent': $this->share['route']= $magazine.'@updateContent'; break;
                    case 'editPrice': $this->share['route']= $magazine.'@updatePrice'; break;
                    case 'newProduct': $this->share['route']= $magazine.'@updateTitle'; break;
                    case 'newPost': $this->share['route']= $magazine.'@updateTitle'; break;
                }
                $this->trigger('default', $this->share['route']);
                return;
            }
            

            if(!empty($this->update->message->photo) || !empty($this->update->message->video) || !empty($this->update->message->document)){
                switch( $section ){
                    case 'newPic': $this->share['route']= $magazine.'@storePic'; break;
                }
                $this->trigger('default', $this->share['route']);
                return;
            }

        }

        if(!empty($this->update->message->text)){
            switch($this->update->message->text){
                case 'مدیریت دسته ها': $this->share['route']= 'adminCategories@index'; break;
                case 'مدیریت محصولات': $this->share['route']= 'adminProducts@showCats'; break;
                case 'مدیریت بلاگ': $this->share['route']= 'adminPosts@index';$this->meet['magazine']=['name'=>'adminPosts','postType'=>'blog']; break;
                case 'مدیریت صفحات': $this->share['route']= 'adminPosts@index';$this->meet['magazine']=['name'=>'adminPosts','postType'=>'page']; break;
                case 'مدیریت گزارشات': $this->share['route']= 'adminReports@index'; break;
                case 'اطلاع رسانی': $this->share['route']= 'adminNotices@index'; break;
                case 'آموزش کار با پنل': $this->share['route']= 'adminNotices@index'; break;
                case 'خروج از مدیریت': $this->share['route']= 'adminPanel@logout'; break;
                default : $this->share['route']='sayHello@adminMenu'; break;
            }
            $this->trigger('default', $this->share['route']);
        }
        return;
    }
}

$this->trigger('default', 'sayHello@mainMenu');
