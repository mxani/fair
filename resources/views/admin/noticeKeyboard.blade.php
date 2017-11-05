{
    "inline_keyboard":[
        @if(!empty($id))
            [
                {
                    "text":"لغو",
                    "callback_data":"{!! interlink(["goto"=>"adminNotices@deleteNotice","id"=>$id])!!}"
                },
                {
                    "text":"تایید و ارسال",
                    "callback_data":"{!! interlink(["goto"=>"adminNotices@sendNotice","id"=>$id])!!}"
                }
            ]
        @else
                [
                    {
                        "text":"📢 ایجاد اطلاع رسانی جدید",
                        "callback_data":"{!! interlink(["goto"=>"adminNotices@addNotice"])!!}"
                    }
                ]
        @endif    
    ]
}