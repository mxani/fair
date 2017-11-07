{
    "inline_keyboard":[
    @if(isset($nextpic)||isset($prevpic))
        [
        @isset($nextpic)
            {
                "text":"تصویر بعدی",
                "callback_data":"{!! interlink(["goto"=>"Suggest@productShow","id"=>$flow,"count"=>$count,"pic"=>$nextpic])!!}"
            }
        @endisset
        @if(isset($nextpic)&&isset($prevpic))
        ,
        @endif
        @isset($prevpic)
            {
                "text":"تصویر قبلی",
                "callback_data":"{!! interlink(["goto"=>"Suggest@productShow","id"=>$flow,"count"=>$count,"pic"=>$prevpic])!!}"
            }
        @endisset
        ],
    @endif
        [
            {
                "text":"بفرمایید",
                "url":"http://t.me/{!! config('XBtelegram.bot-username') !!}"
            },
            {
                "text":"پیشنهاد به دوستان",
                "switch_inline_query":"{{$flow}}"
            }
        ]
    ]
}