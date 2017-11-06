<?php

namespace App\Magazines;

use XB\theory\Magazine;
use App\Model\Category;
use App\Model\Product;
use XB\telegramMethods\sendMessage;
use XB\telegramMethods\editMessageText;

class mstProducts extends Magazine{
    public function main(){
        $message=['chat_id'=>$this->update->callback_query->from->id,
        'message_id'=>$this->update->callback_query->message->message_id,'parse_mode'=>'html'];

        ///> validation palce is here. probably don't need to check <///

        $keyBpara=['cat'=>$this->detect->data->cat];
        $category = Category::find($keyBpara['cat']);

        $selected_product_id=$this->detect->data->id??false;
        $pic=$product=null;
        $products=$category->products();
        if($selected_product_id){
            $products=$products->where('products.id','<=',$selected_product_id);
        }
        $products=$products->orderby('id','desc')->take(2)->get();

        if(!empty($products[0])){
            $product=$products[0];
            $keyBpara['flow']=$product->id;
            $pic=$this->detect->data->pic??0;
            $keyBpara['prevpic']=empty($product->files[$pic-1])?null:$pic-1;
            $keyBpara['nextpic']=empty($product->files[$pic+1])?null:$pic+1;
            $keyBpara['orderable']=$product->orderable;
            
            $back=$category->products()->where('products.id','>',$products[0]->id)->orderby('id','asc')->first();
            $keyBpara['prev']=$back->id??null;
        }
        if(!empty($products[1])){
            $keyBpara['next']=$products[1]->id;
        }


        $message['text']=view('productMessage',['product'=>$product,'pic'=>$pic])->render();
        $message['reply_markup']=view('master.productKeyboard',$keyBpara)->render();
        $send=new editMessageText($message);
        $send();
    }
    
}
