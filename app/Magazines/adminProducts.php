<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Category;
use App\Model\Product;
use App\Model\ProductCategory;
use XB\telegramMethods\editMessageText;
use XB\telegramMethods\deleteMessage;

class adminProducts extends Magazine
{

    protected $text = '';

    ############# Display Categories #############
    public function showCats()
    {
        
        $categories = Category::get(['name','id'])->toarray();

        $msg_text =  $this->text."لطفا یک دسته انتخاب کنید.";
        $msg_reply_markup = view('admin.categoryKeyboard', ['goto'=>"adminProducts@index",'items'=>$categories])->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
    }

    ############# Display Products #############
    public function index($category_id = null, $current_pid = null)
    {

        if (!empty($this->detect->data->cat_id)) {
            $category_id = (int)$this->detect->data->cat_id;
        } else {
            $category_id =(int)$category_id;
        }
        
        $keyBpara['cat_id'] = $category_id;
        $keyBpara['goto'] = 'adminProducts@index';
        $category = Category::find($category_id);

        if (!empty($this->detect->data->id)) {
            $selected_product_id = $this->detect->data->id;
        } elseif (!empty($current_pid)) {
            $selected_product_id = $current_pid;
        } else {
            $selected_product_id = false;
        }

        //$selected_product_id=$this->detect->data->id??false;
        $pic=$product=null;
        $products=$category->products();
        if ($selected_product_id) {
            $products=$products->where('products.id', '<=', $selected_product_id);
        }
        $products=$products->orderby('id', 'desc')->take(2)->get();
        
        if (!empty($products[0])) {
            $product=$products[0];
            $keyBpara['flow']=$product->id;
            $pic=$this->detect->data->pic??0;
            $keyBpara['pic'] = $pic;
            $keyBpara['prevpic']=empty($product->files[$pic-1])?null:$pic-1;
            $keyBpara['nextpic']=empty($product->files[$pic+1])?null:$pic+1;
            
            $back=$category->products()->where('products.id', '>', $products[0]->id)->orderby('id', 'asc')->first();
            $keyBpara['prev']=$back->id??null;
        }
        if (!empty($products[1])) {
            $keyBpara['next']=$products[1]->id;
        }

        $msg_text =  view('productMessage', ['product'=>$product,'pic'=>$pic])->render();
        $msg_reply_markup = view('admin.productKeyboard', $keyBpara)->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
    }

    ############# Add a Picture #############
    public function newPic()
    {
        if (empty($this->meet['section']['state'])) {
            $this->meet['section'] = ['name'=>'newPic','id'=>$this->detect->data->id, 'cat_id'=>$this->detect->data->cat_id];
        } else {
            $this->meet['section'] = ['name'=>'newPic','id'=>$this->meet['section']['id'], 'cat_id'=>$this->meet['section']['cat_id']];
        }

        if (!empty($this->meet['section']['id'])) {
            $id = $this->meet['section']['id'];
        } else {
            $id = $this->detect->data->id;
        }
        $product = Product::find($id);

        $message['chat_id'] = $this->detect->from->id;
        $message['text'] = "یک تصویر جدید برای محصول <code>$product->title</code> ارسال کنید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }
    public function storePic()
    {
        if (!empty($this->update->message->photo)) {
            $file = $this->update->message->photo;
            $file = $file[count($file)-1];
            $file_id = $file->file_id;
        } elseif (!empty($this->update->message->video)) {
            $file = $this->update->message->video;
            $file_id = $file->file_id;
        } elseif (!empty($this->update->message->document)) {
            $file = $this->update->message->document;
            $file_id = $file->file_id;
        }

        //$url = $this->get_url();

        // generate fake image url
        $faker = \Faker\Factory::create('fa_IR');
        $fake_image = $faker->imageurl;
        // add fake_image to product files
        $product = Product::find($this->meet['section']['id']);
        $current_files = [];
        if($product->files){
            $current_files = $product->files;
        }
        array_unshift($current_files, $fake_image);
        $product->files = array_values($current_files);
        $product->update();

        $category_id = $this->meet['section']['cat_id'];
        $current_pid = $this->meet['section']['id'];
        $this->index( $category_id, $current_pid );
        unset($this->meet['section']);
    }

    ############# Remove Picture #############
    public function removePic()
    {
        $id = $this->detect->data->id;
        $pic_index = $this->detect->data->pic;

        $product = Product::find($id);
        $files = $product->files;
        unset($files[$pic_index]);
        $product->files = array_values($files);
        $product->update();
      
        $this->index();
    }

    ############# Edit Title #############
    public function editTitle()
    {
        $this->meet['section'] = ['name'=>'editTitle','id'=>$this->detect->data->id, 'cat_id'=>$this->detect->data->cat_id];

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "عنوان جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }
    public function updateTitle()
    {
        $product = Product::find($this->meet['section']['id']);
        $product->title = $this->update->message->text;
        $product->update();

        if (!empty($this->meet['section']['state'] )) {
            $this->editContent();
        } else {
            $category_id = $this->meet['section']['cat_id'];
            $current_pid = $this->meet['section']['id'];
            $this->index( $category_id, $current_pid );
            unset($this->meet['section']);
        }
    }

    ############# Edit Description #############
    public function editContent()
    {
        if (empty($this->meet['section']['state'] )) {
            $this->meet['section'] = ['name'=>'editContent','id'=>$this->detect->data->id, 'cat_id'=>$this->detect->data->cat_id];
        } else {
            $this->meet['section'] = ['name'=>'editContent','id'=>$this->meet['section']['id'], 'cat_id'=>$this->meet['section']['cat_id'],'state'=>'create'];
        }
        
        $chat_id = $this->detect->from->id;
        if ($this->detect->type == 'callback_query') {
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }
        
        $message['chat_id'] = $chat_id;
        $message['text'] = "توضیحات جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }
    public function updateContent()
    {
        $product = Product::find($this->meet['section']['id']);
        $product->description = $this->update->message->text;
        $product->update();

        if (!empty($this->meet['section']['state'] )) {
            $this->editPrice();
        } else {
            $category_id = $this->meet['section']['cat_id'];
            $current_pid = $this->meet['section']['id'];
            $this->index( $category_id, $current_pid );
            unset($this->meet['section']);
        }
    }

    ############# Edit Price #############
    public function editPrice()
    {
        
        if (empty($this->meet['section']['state'])) {
            $this->meet['section'] = ['name'=>'editPrice','id'=>$this->detect->data->id, 'cat_id'=>$this->detect->data->cat_id];
        } else {
            $this->meet['section'] = ['name'=>'editPrice','id'=>$this->meet['section']['id'], 'cat_id'=>$this->meet['section']['cat_id'],'state'=>'create'];
        }
        
        $chat_id = $this->detect->from->id;
        if ($this->detect->type == 'callback_query') {
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "قیمت جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }
    public function updatePrice()
    {
        $product = Product::find($this->meet['section']['id']);
        $product->price = $this->update->message->text;
        $product->update();

        if (!empty($this->meet['section']['state'] )) {
            $this->newPic();
        } else {
            $category_id = $this->meet['section']['cat_id'];
            $current_pid = $this->meet['section']['id'];
            $this->index( $category_id, $current_pid );
            unset($this->meet['section']);
        }
    }

    ############# Delete Product #############
    public function destroy()
    {
        $product = Product::find($this->detect->data->id);

        if ($product->delete()) {
            $this->text =  "محصول <code>".$product->title."</code> با موفقیت حذف شد. \n";
            $category_id = $this->detect->data->cat_id;
            $this->index( $category_id);
        }
    }

    ############# Add New Product #############
    public function newProduct()
    {
        
        $product = Product::create([
            'title' => 'عنوان محصول',
            'description' => 'توضیحات محصول',
            'price' => 0,
            ]);
        $product->category()->attach($this->detect->data->cat_id);
            
        $this->meet['section'] = ['name'=>'newProduct','id'=>$product->id, 'cat_id'=>$this->detect->data->cat_id,'state'=>'create'];

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "عنوان جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = '{"force_reply":true}';
        (new sendMessage($message))->call();
    }


    ## send or edit message ##
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

    ## getting the url of uploaded image in telegram ##
    private function get_url($file_id)
    {
        $get =  new \XB\telegramMethods\getFile(['file_id'=>$file_id]);
        $get();
        if (!empty($path = $get->result->file_path)) {
            $url="https://api.telegram.org/file/bot".config('XBtelegram.bot-token')."/$path";
        }
        
        return $url;
    }
}
