<?php

namespace App\Magazines;

use XB\theory\Magazine;
use App\Model\Person;
use XB\telegramMethods\sendMessage;

class adminReports extends Magazine
{
    protected $text = '';
    public function index()
    {
        $people = Person::all();
        $total_count = $people->count();

        $today = [];

        $today = date("Y-m-d");
        $yesterday = date("Y-m-d", strtotime('yesterday'));
        $lastweek = date("Y-m-d", strtotime('last week'));

        // get today registration
        $today_count = $people->filter(function ($item) use ($today) {
             return date('Y-m-d', strtotime($item->created_at)) == $today;
        })->count();
        
        // get yesterday registration
        $yesterday_count = $people->filter(function ($item) use ($yesterday) {
            return date('Y-m-d', strtotime($item->created_at)) == $yesterday;
        })->count();

        // get lastweek registration
        $lestweek_count = $people->filter(function ($item) use ($today, $lastweek) {
            return date('Y-m-d', strtotime($item->created_at)) < $today && date('Y-m-d', strtotime($item->created_at)) > $lastweek;
        })->count();

        $report = ['today'=>$today_count, 'yesterday'=>$yesterday_count, 'lastweek'=>$lestweek_count];
        //$p = Person::whereDate('created_at', '=', $yesterday)->get()->count();

        $msg_text =  view('admin.reportMessage', ['report'=>$report])->render();
        $this->my_sendMessage($msg_text);
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
