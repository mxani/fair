<?php
use XB\theory\Shoot;
use XB\telegramMethods\sendMessage;

if (empty($this->detect->tenant)) {
        require_once('master.php');
        return;
}
DB::purge('mysql');
if($this->detect->type == "callback_query" && !empty($this->detect->data->goto)){
    Shoot::trigger('default',$this->detect->data->goto);
    return ;
}

if ($this->detect->type=='inline_query') {
    $this->trigger('default', 'Suggest');
    return;
}

if ($this->detect->from->id == config('owner_id') && ($this->update->message->text??'') == '*admin' || ($this->update->message->text??'') == '*مدیریت') {
   
    $expired_date=date_create(config('expires_at'));
    $now=date_create();
    $diff=date_diff($now,$expired_date);
    if($diff->invert == 1){ // expired

        $send=new sendMessage([
            'chat_id'=>config('owner_id'),
            'text'=>" مهلت استفاده از ربات به پایان رسیده است.\n لطفا جهت تمدید به آدرس زیر مراجعه کنید. \n @telerobotic_bot",
            'parse_mode'=>'html',
            ]);
        $send();

        $this->trigger(true, 'adminPanel@logout');
        return;
    }

    if($diff->days < 4){
        $send=new sendMessage([
            'chat_id'=>config('owner_id'),
            'text'=>"⚠ ️مهلت استفاده از ربات : <code>$diff->days</code> روز و <code>$diff->h</code> ساعت و <code>$diff->i</code> دقیقه ⚠️",
            'parse_mode'=>'html',
            ]);
        $send();
    }
    $this->meet['mod'] = 'admin';// logged in

}

if (($this->meet['mod'] ??'') != 'admin') {
    ## customer

    if ($this->detect->type=='message'&&$this->detect->msgtype=='text') { 

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

// if action was canceled 
if(!empty($this->meet['cancel'])&&($this->update->message->text??'') =='انصراف'){
    $cancel=$this->meet['cancel'];
    if(!empty($this->meet['section']['cat_id'])){
        $cat_id = $this->meet['section']['cat_id'];
        $id = $this->meet['section']['id']??null;
        $this->meet['section'] = '';
        $this->meet['section']=['cat_id'=> $cat_id, 'id'=> $id];
    }elseif(!empty($this->meet['section']['id'])){
        $id = $this->meet['section']['id'];
        $this->meet['section'] = '';
        $this->meet['section']=['id'=> $id];
    }else{
        unset($this->meet['section']);
    }
    unset($this->meet['cancel']);
    $this->trigger(function(){return true;},$cancel);
    $this->trigger(function(){return true;},'sayHello@adminMenu');
    return;
}

if (!empty($this->meet['section'])) {
        $this->trigger('default', $this->meet['section']['route']);
        return;
}

// admin menu
if (!empty($this->update->message->text)) {
    switch ($this->update->message->text) {
        case 'مدیریت دسته ها': $this->share['route']= 'adminCategories@index'; break;
        case 'مدیریت محصولات': $this->share['route']= 'adminProducts@showCats'; break;
        case 'مدیریت بلاگ': $this->share['route']= 'adminPosts@index'; $this->meet['magazine']=['name'=>'adminPosts','postType'=>'blog']; break;
        case 'مدیریت صفحات': $this->share['route']= 'adminPosts@index'; $this->meet['magazine']=['name'=>'adminPosts','postType'=>'page']; break;
        case 'مدیریت گزارشات': $this->share['route']= 'adminReports@index'; break;
        case 'اطلاع رسانی': $this->share['route']= 'adminNotices@index'; break;
        case 'آموزش کار با پنل': $this->share['route']= 'adminPanel@guide'; break;
        case 'خروج از مدیریت': $this->share['route']= 'adminPanel@logout'; break;
        default: $this->share['route']='sayHello@adminMenu'; break;
    }
    $this->trigger('default', $this->share['route']);
}