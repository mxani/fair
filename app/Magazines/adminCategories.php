<?php

namespace App\Magazines;

use XB\theory\Magazine;
use App\Model\Category;
use App\Model\ProductCategory;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;
use XB\telegramMethods\deleteMessage;

class adminCategories extends Magazine
{
    protected $text = '';
    public function index()
    {
        $categories = Category::get(['name','id'])->toarray();

        $msg_text =  $this->text."لطفا یک دسته برای <code>ویرایش</code> انتخاب کنید.";
        $msg_reply_markup = view('admin.categoryKeyboard', ['goto'=>"adminCategories@show",'items'=>$categories])->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
    }

    public function show()
    {
        $category = Category::where('id', $this->detect->data->cat_id)->first()->toArray();
        $msg_text =  "نام دسته :  <code>".$category['name']."</code>";
        $msg_reply_markup = view('admin.categoryKeyboard', ['category'=>$category['id'],'backToList'=>true,'mode'=>'editKB'])->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
    }

    public function add()
    {
        $this->meet['section'] = ['name'=>'newCat','route'=>'adminCategories@store'];

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "نام دسته جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }

    public function store()
    {
        $newCat = $this->update->message->text;

        $category = Category::create([
            'name'=>$newCat ,
            'parent_id' => null
            ]);

        if ($category) {
            unset($this->meet['section']);
            $this->text =  "دسته <code>".$category->name."</code> با موفقیت ایجاد شد. \n";
            $this->index();
            $this->caller(sayHello::class)->adminMenu();
        }
    }

    public function edit()
    {
        $this->meet['section'] = ['name'=>'editCat','route'=>'adminCategories@update', 'id'=>$this->detect->data->cat_id];
        $category = Category::where('id', $this->detect->data->cat_id)->first()->toArray();

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "نام جدید برای دسته  <code>".$category['name']."</code>  وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }

    public function update()
    {
        $category = Category::find($this->meet['section']['id']);
        $category->name = $this->update->message->text;
        if ($category->update()) {
            unset($this->meet['section']);
            $this->index();
            $this->caller(sayHello::class)->adminMenu();
        }
    }

    public function destroy()
    {
        $category = Category::find($this->detect->data->cat_id);

        if ($category->products->count() > 0) {
            $products = ProductCategory::where('cat_id', $category->id)->get();
            $products->map(function ($item) {
                $item->cat_id = 1;
                $item->update();
            });
        
            $defaultCat = Category::find(1);
            $this->text =  "و محصولات زیرمجموعه آن به دسته <code>$defaultCat->name</code> انتقال یافت. \n";
        }

        if ($category->delete()) {
            $this->text =  "دسته <code>$category->name</code> با موفقیت حذف شد. \n".$this->text;
            $this->index();
        }
    }

    private function my_sendMessage($text, $reply_markup = null)
    {
        $message=['chat_id'=>$this->detect->from->id,'parse_mode' => 'HTML'];
        $api=sendMessage::class;
        if ($this->detect->type == 'callback_query') {
            $api=editMessageText::class;
            $message['message_id'] = $this->update->callback_query->message->message_id;
        }

        $message['text'] = $text;
        if ($reply_markup!=null) {
            $message['reply_markup'] = $reply_markup;
        }
        (new $api($message))->call();
    }
}
