{
    "inline_keyboard":[
    @if(count($items)==0)
        [
            {
                "text":"نمایش محصولات",
                "callback_data":"{!! interlink(["goto"=>"mstCategories"])!!}"
            }
        ]
    @else
        @php
            $enditem=count($items)%2?array_pop($items):null;
            $count=count($items)
        @endphp
        @for($i=0;$i<$count;$i+=2)
            @if($i)
            ,
            @endif
            [
                {
                    "text":"{{$items[$i]['detail']['first_name']}}",
                    "callback_data":"{!! interlink(["goto"=>"myBots@show","tenant"=>$items[$i]['id']])!!}"
                },
                {
                    "text":"{{$items[$i+1]['detail']['first_name']}}",
                    "callback_data":"{!! interlink(["goto"=>"myBots@show","tenant"=>$items[$i+1]['id']])!!}"
                }
            ]
        @endfor
        @isset($enditem)
            @if($count)
            ,
            @endif
            [
                {
                    "text":"{{$enditem['detail']['first_name']}}",
                        "callback_data":"{!! interlink(["goto"=>"myBots@show","tenant"=>$enditem['id']])!!}"
                }
            ]
        @endisset
    @endif
    
    ]
}