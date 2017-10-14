{
    "inline_keyboard":[
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
                "text":"{{$items[$i]['name']}}",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$items[$i]['id']])!!}"
            },
            {
                "text":"{{$items[$i+1]['name']}}",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$items[$i+1]['id']])!!}"
            }
        ]
    @endfor
    @isset($enditem)
        ,[
            {
                "text":"{{$enditem['name']}}",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$enditem['id']])!!}"
            }
        ]
    @endisset
    ]
}