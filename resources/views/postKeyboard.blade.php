{
    "inline_keyboard":[
    @if(!empty($current_id))        
        @if(isset($next) || isset($prev))
        
            [
                @isset($next)
                {
                    "text":"{!! $next_title !!}",
                    "callback_data":"{!! interlink(["goto"=>"Posts@blog","id"=>$next])!!}"
                }
                @endisset
            ]

            @isset($prev)
            ,
            [
                {
                    "text":"{!! $prev_title !!}",
                    "callback_data":"{!! interlink(["goto"=>"Posts@blog","id"=>$prev])!!}"
                }
            ]
            @endisset
            
            
        @endif
    @endif
    ]
}