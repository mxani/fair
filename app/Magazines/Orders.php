<?php

namespace App\Magazines;

use XB\theory\Magazine;
use XB\telegramMethods\sendMessage;
use App\Model\Person;
use App\Model\Product;
use App\Model\Master\Order;
use App\Model\Master\Tenant;

class Orders extends Magazine{
    use \App\Helpers\Master\BotToken;

    public function main(){
        $person=$this->share['person']??Person::where('telegramID',$this->detect->from->id)->first();
        if(empty($person)){
            $this->caller(mstGreet::class)->main();
            return;
        }

        $id=$this->meet['order']['product_id']=
            $this->detect->data->id??
            $this->meet['order']['product_id']??
            null;
        $product=Product::find($id);
        if(empty($id)|| empty($product)|| !$product->orderable){
            $this->caller(mstGreet::class)->mainMenu();
            return;
        }

        if(empty($person->detail['phone_number'])){
            $this->meet['request']['ref_mag']='Orders';
            $this->meet['request']['ref_car']='main';
            $this->caller(request::class)->contact();
            return;
        }

        if($person->tenants()->where('tenants.status','trial')->count()>=1){
            $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
            $message['text']=view('master.orderLimitMessage',$person->detail)->render();
            $send=new sendMessage($message);
            $send();
            $this->caller(myBots::class)->main();
            return;
        }

        $this->showPrivacy();
    }

    public function showPrivacy(){

        $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        $message['text']=view('master.privacyMessage')->render();
        $message['reply_markup']=view('master.privacyMenu')->render();
        $this->meet['goto']='Orders@confirmPrivacy';
        $send=new sendMessage($message);
        $send();
    }

    public function confirmPrivacy(){
        $botToken=$this->update->message->text??'';
        if($botToken=='بیخیال شدم'){
            $this->caller(mstGreet::class)->mainMenu();
            return;
        }
        if($botToken!='موافقم'){
            $this->showPrivacy();
            return;
        }

        $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        $message['text']=view('master.neworderMessage')->render();
        $message['reply_markup']=view('master.defaultMenu')->render();
        $this->meet['goto']='Orders@getToken';
        $send=new sendMessage($message);
        $send();
    }

    public function getToken(){
        $botToken=$this->update->message->text??'';
        if($botToken=='بیخیال شدم'){
            $this->caller(mstGreet::class)->mainMenu();
            return;
        }

        if(!preg_match("/\d+:\S+/", $botToken,$botToken) || false===$bot=$this->isValidToken($botToken[0])){
            $this->meet['goto']='Orders@getToken';
            $this->invalidToken();
            return;
        }

        $botToken=$botToken[0];

        $product=Product::find($this->meet['order']['product_id']);
        if(empty($product)|| !$product->orderable){
            $this->caller(mstGreet::class)->mainMenu();
            return;
        }

        $bot=json_decode($bot,true)['result'];

        $order = new Order;
        $order->person_id=$this->share['person']->id;
        $order->product_id=$product->id;
        $order->price=0;
        $order->detail=[];
        $order->save();

        do{
            $tenantToken=str_random(30);
        }while(\File::exists($tenantPath="bot/tenants/$tenantToken"));

        \File::makeDirectory("$tenantPath/logs",0755,true);
        \File::makeDirectory("$tenantPath/meetings");

        $tenant= new Tenant;
        $tenant->order_id=$order->id;
        $tenant->bot_token=$botToken;
        $tenant->token=$tenantToken;
        $tenant->detail=$bot;
        $tenant->status='trial';
        $tenant->expires_at=\Carbon\Carbon::now()->adddays(14);
        $tenant->save();

        $pass=str_random(20);

        try{
            \DB::select("CREATE SCHEMA `tenant_{$tenant->id}_db` DEFAULT CHARACTER SET utf8 ;");
            \DB::select("create user 'tenant_{$tenant->id}_u'@'localhost' identified by '$pass';");
            \DB::select("grant all privileges on `tenant_{$tenant->id}_db`.* to 'tenant_{$tenant->id}_u'@'localhost';");
            
        }catch(Exception $e){
            $this->errorHappen('db init.');
            return;
        }


        $configs="
            'owner_id'=>{$this->detect->from->id},
            'status'=>'trial',
            'expires_at'=>'{$tenant->expires_at}',
            'XBtelegram.bot-token'=>'$botToken',
            'XBtelegram.bot-username'=>'{$bot['username']}',
            'database.connections.mysql.database'=>'tenant_{$tenant->id}_db',
            'database.connections.mysql.username'=>'tenant_{$tenant->id}_u',
            'database.connections.mysql.password'=>'$pass',
        ";
        \File::put("$tenantPath/configs.php","<?php return[$configs];");

        $configs=\File::getRequire("$tenantPath/configs.php");
        $master=[];
        foreach($configs as $k => $v){
            $master[$k]=config($k);
        }

        $message=['chat_id'=>$this->detect->from->id,'parse_mode'=>'html'];
        $message['text']="درحال اتصال به ربات شما.\nاز اینکه با صبر و شکیباییتان ما رو همراهی کنید، صمیمانه سپاس گذاریم.🌺 ";
        (new sendMessage($message))->call();

        config($configs);
        \DB::purge('mysql');

        $message['text']='این یک پیام تستی میباشد.';
        (new sendMessage($message))->call();
        
        \Artisan::call('migrate',['--path'=>'bot/migrations']);
        \Artisan::call('db:seed',['--class'=>'dummyDataSeeder']);

        $api=new \XB\telegramMethods\setWebhook([
            'url'=>config('XBtelegram.host')."/telegram.php?tenant=$tenant",
            'certificate'=>base_path('certify.crt'),
            'max_connections'=>80,
        ]);
        $api('certificate');

        config($master);
        \DB::purge('mysql');
        
        unset($this->meet['goto']);

        $message['text']=view('master.greetBotMessage',$bot)->render();
        $send=new sendMessage($message);
        $send();
        $this->caller(mstGreet::class)->mainMenu();
    }
    







    ///> privates

}
