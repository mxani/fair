{
    "keyboard": [
        @if(!empty($customer))
        [
            {
                "text":"روباتهای من"
            }
        ],
        @endif
        [
            {
                "text": "بلاگ"
            },
            {
                "text": "محصولات"
            }
        ]
    @php
        $items = \App\Model\Post::wheretype('page')->get()->toArray();
        $enditem=count($items)%2?array_pop($items):null;
        $count=count($items)
    @endphp
    @for($i=0;$i<$count;$i+=2)
        ,
        [
            {
                "text":"{{$items[$i]['title']}}"
            },
            {
                "text":"{{$items[$i+1]['title']}}"
            }
        ]
    @endfor
    @isset($enditem)
        ,[
            {
                "text":"{{$enditem['title']}}"
            }
        ]
    @endisset
    ],
    "resize_keyboard": true,
    "one_time_keyboard": true
}