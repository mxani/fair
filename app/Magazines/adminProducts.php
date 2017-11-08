<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Category;
use App\Model\Product;
use App\Model\ProductCategory;
use XB\telegramMethods\editMessageText;
use XB\telegramMethods\deleteMessage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;

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
            $category_id = $this->detect->data->cat_id;
        } elseif (!empty($this->meet['section']['cat_id'])) {
            $category_id = $this->meet['section']['cat_id'];
            unset($this->meet['section']);
        } else {
            $category_id = $category_id;
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

        if ($category->products->count() == 1) {
            $product = $category->products->first();
            $keyBpara['flow']=$product->id;
            $pic=$this->detect->data->pic??0;
            $pic = empty($product->files[$pic]) ? null : $pic;
            $keyBpara['pic'] = $pic;
            $keyBpara['prevpic']=empty($product->files[$pic-1])?null:$pic-1;
            $keyBpara['nextpic']=empty($product->files[$pic+1])?null:$pic+1;
        } else {
            $products=$category->products();
            if ($selected_product_id) {
                $products=$products->where('products.id', '<=', $selected_product_id);
            }
            $products=$products->orderby('id', 'desc')->take(2)->get();
        
            if (!empty($products[0])) {
                $product=$products[0];
                $keyBpara['flow']=$product->id;
                $pic=$this->detect->data->pic??0;
                $pic = empty($product->files[$pic]) ? null : $pic;
                $keyBpara['pic'] = $pic;
                $keyBpara['prevpic']=empty($product->files[$pic-1])?null:$pic-1;
                $keyBpara['nextpic']=empty($product->files[$pic+1])?null:$pic+1;
            
                $back=$category->products()->where('products.id', '>', $products[0]->id)->orderby('id', 'asc')->first();
                $keyBpara['prev']=$back->id??null;
            }
            if (!empty($products[1])) {
                $keyBpara['next']=$products[1]->id;
            }
        }

        $msg_text = $this->text."محصولی برای نمایش وجود ندارد.";
        if (!empty($product)) {
            $msg_text = view('productMessage', ['product'=>$product,'pic'=>$pic])->render();
        }
        $msg_reply_markup = view('admin.productKeyboard', $keyBpara)->render();
        $this->my_sendMessage($msg_text, $msg_reply_markup);
    }

    ############# Add a Picture #############
    public function newPic()
    {
        $this->meet['section'] = ['name'=>'newPic','route'=>'adminProducts@storePic','id'=>$this->detect->data->id, 'cat_id'=>$this->detect->data->cat_id];
       
        if (!empty($this->meet['section']['id'])) {
            $id = $this->meet['section']['id'];
        } else {
            $id = $this->detect->data->id;
        }
        $product = Product::find($id);

        $message['chat_id'] = $this->detect->from->id;
        $message['text'] = "یک تصویر جدید برای محصول <code>$product->title</code> ارسال کنید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = view('cancleMenu')->render();
        $this->meet['cancel']='adminProducts@index';
        (new sendMessage($message))->call();
    }
    public function storePic()
    {
        unset($this->meet['cancel']);
        $product_id = $this->meet['section']['id'];

        if (!empty($this->update->message->photo)) {
            $file = $this->update->message->photo;
            $file = $file[count($file)-1];
            $file_id = $file->file_id;
        } else {
            $message['chat_id'] = $this->detect->from->id;
            $message['text'] = "لطفا فقط یک تصویر را با در نظر گرفتن حالت <code>فشرده (compress)</code> ارسال کنید";
            $message['parse_mode'] = 'html';
            (new sendMessage($message))->call();
            return;
        }
        
        if (!empty($file_id)) {
            $newImage = $this->get_url($file_id);
            /*
            // generate fake image url
            $faker = \Faker\Factory::create('fa_IR');
            $fake_image = $faker->imageurl;
            */
            $product = Product::find($product_id);
            $current_files = [];
            if ($product->files) {
                $current_files = $product->files;
            }
            array_unshift($current_files, $newImage);
            $product->files = array_values($current_files);
            $product->update();
        }

        $category_id = $this->meet['section']['cat_id'];
        //$current_pid = $this->meet['section']['id'];
        $this->index( $category_id, $product_id );
        unset($this->meet['section']);
        $this->caller(sayHello::class)->adminMenu();
    }

    ############# Remove Picture #############
    public function removePic()
    {
        $id = $this->detect->data->id;
        $pic_index = $this->detect->data->pic;

        $product = Product::find($id);
        $files = $product->files;
        
        $lastpic = end($files);
        $isLastPic = false;
        if ($product->files[$pic_index]  == $lastpic) {
            $isLastPic = true;
        }

        unset($files[$pic_index]);
        $product->files = array_values($files);
        $product->update();

        if ($isLastPic) {
            $files = $product->files;
            end($files);
            $lastpicKey = key($files);
            $this->detect->data->pic = $lastpicKey;
        }
     
        $this->index();
    }

    ############# Edit Title #############
    public function editTitle()
    {
        $this->meet['section'] = ['name'=>'editTitle','route'=>'adminProducts@updateTitle','id'=>$this->detect->data->id, 'cat_id'=>$this->detect->data->cat_id];

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "عنوان جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = view('cancleMenu')->render();
        $this->meet['cancel']='adminProducts@index';
        (new sendMessage($message))->call();
    }
    public function updateTitle()
    {
        unset($this->meet['cancel']);
        if (($this->meet['section']['state'])??'' == 'create') {
            $product = Product::create([
                'title' => $this->update->message->text??'عنوان محصول',
                'description' => 'توضیحات محصول',
                'price' => 0,
                ]);
            $product->category()->attach($this->meet['section']['cat_id']);
            $product_id = $product->id;
        }

        if(!empty($this->meet['section']['id'])){
            $product = Product::find($this->meet['section']['id']);
            $product->title = $this->update->message->text??$product->title;
            $product->update();
            $product_id = $product->id;
        }

        $category_id = $this->meet['section']['cat_id'];
        //$current_pid = $this->meet['section']['id'];
        $this->index( $category_id, $product_id );
        unset($this->meet['section']);
        $this->caller(sayHello::class)->adminMenu();
    }

    ############# Edit Description #############
    public function editContent()
    {
        $this->meet['section'] = ['name'=>'editContent','route'=>'adminProducts@updateContent','id'=>$this->detect->data->id, 'cat_id'=>$this->detect->data->cat_id];
        
        $chat_id = $this->detect->from->id;
        if ($this->detect->type == 'callback_query') {
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }
        
        $message['chat_id'] = $chat_id;
        $message['text'] = "توضیحات جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = view('cancleMenu')->render();
        $this->meet['cancel']='adminProducts@index';
        (new sendMessage($message))->call();
    }
    public function updateContent()
    {
        unset($this->meet['cancel']);
        $product_id = $this->meet['section']['id'];
        $product = Product::find($product_id);
        $product->description = $this->update->message->text??$product->description;
        $product->update();


            $category_id = $this->meet['section']['cat_id'];
            //$current_pid = $this->meet['section']['id'];
            $this->index( $category_id, $product_id );
            unset($this->meet['section']);
            $this->caller(sayHello::class)->adminMenu();
    }

    ############# Edit Price #############
    public function editPrice()
    {        
        $this->meet['section'] = ['name'=>'editPrice','route'=>'adminProducts@updatePrice','id'=>$this->detect->data->id, 'cat_id'=>$this->detect->data->cat_id];
        
        $chat_id = $this->detect->from->id;
        if ($this->detect->type == 'callback_query') {
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "قیمت جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = view('cancleMenu')->render();
        $this->meet['cancel']='adminProducts@index';
        (new sendMessage($message))->call();
    }
    public function updatePrice()
    {
        unset($this->meet['cancel']);
        $product_id = $this->meet['section']['id'];

        $product = Product::find($product_id);
        $product->price = $this->update->message->text ?? $product->price;
        $product->update();


            $category_id = $this->meet['section']['cat_id'];
            //$current_pid = $this->meet['section']['id'];
            $this->index( $category_id, $product_id );
            unset($this->meet['section']);
            $this->caller(sayHello::class)->adminMenu();
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
        $this->meet['section'] = ['name'=>'newProduct','route'=>'adminProducts@updateTitle','cat_id'=>$this->detect->data->cat_id,'state'=>'create'];

        if ($this->detect->type == 'callback_query') {
            $chat_id = $this->detect->from->id;
            $message_id = $this->update->callback_query->message->message_id;
            (new deleteMessage(['chat_id'=>$chat_id,'message_id'=>$message_id]))->call();
        }

        $message['chat_id'] = $chat_id;
        $message['text'] = "عنوان جدید را وارد نمایید:";
        $message['parse_mode'] = 'html';
        $message['reply_markup'] = view('cancleMenu')->render();
        $this->meet['cancel']='adminProducts@index';
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

        //$this->caller(sayHello::class)->adminMenu();
    }

    ## getting the url of uploaded image in telegram ##
    private function get_url($file_id)
    {
        $get =  new \XB\telegramMethods\getFile(['file_id'=>$file_id]);
        $get();
        if (!empty($path = $get->result->file_path)) {
            $url="https://api.telegram.org/file/bot".config('XBtelegram.bot-token')."/$path";
        }
       
        $client = new Client();
        try {
            $response=$client->request(
                'POST',
                'http://telerobotic.ir/gfftb2017.php',
                ['form_params' =>['fileUrl'=>$url,'tenantToken'=>$this->detect->tenant]]
            );
            $result = $response->getBody()->getContents();
        } catch (ClientException $e) {
            echo 'ClientException: '.$e->getMessage();
            return false;
        } catch (TransferException $e) {
            echo 'TransferException: '.$e->getMessage();
            return false;
        } catch (\RuntimeException $e) {
            echo 'RuntimeException: '.$e->getMessage();
            return false;
        } catch (\Exception $e) {
            echo 'RuntimeException: '.$e->getMessage();
            return false;
        }

        return $result;
    }

    private function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
