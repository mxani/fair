{
    "inline_keyboard":[
        [
            @if($diff->invert==0 &&( $diff->y>0|| $diff->m>5))
            {
                "text":"بله",
                "callback_data":"{!! interlink(["goto"=>"myBots@cancelConfirm",'tenant'=>$tenant->id])!!}"
            }
        ],
        [
            @endif
            {
                "text":"خیر انصراف نمیدم",
                "callback_data":"{!! interlink(["goto"=>"myBots@show",'tenant'=>$tenant->id])!!}"
            }
        ]
    ]
}