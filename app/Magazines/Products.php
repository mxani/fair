<?php

namespace App\Magazines;

use XB\theory\Magazine;
use App\Model\Category;
use App\Model\Product;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;

class Products extends Magazine{
    public function index(){
        $message=['chat_id'=>$this->update->callback_query->from->id,
        'message_id'=>$this->update->callback_query->message->message_id,'parse_mode'=>'html'];

        ///> validation palce is here. probably don't need to check <///

        $para=['cat'=>$this->detect->data->cat];
        $category = Category::find($para['cat']);

        $selected_product_id=$this->detect->data->id??false;
        $product=null;
        $products=$category->products();
        if($selected_product_id){
            $products=$products->where('products.id','<=',$selected_product_id);
        }
        $products=$products->orderby('id','desc')->take(2)->get();

        if(!empty($products[0])){
            $product=$products[0];
            $back=$category->products()->where('products.id','>',$products[0]->id)->orderby('id','asc')->first();
            $para['prev']=$back->id??null;
        }
        if(!empty($products[1])){
            $para['next']=$products[1]->id;
        }


        $message['text']=view('productMessage',['product'=>$product])->render();
        $message['reply_markup']=view('productKeyboard',$para)->render();
        $send=new editMessageText($message);
        $send();
    }
    
}
