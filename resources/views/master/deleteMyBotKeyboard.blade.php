{
    "inline_keyboard":[
        [
            {
                "text":"بله",
                "callback_data":"{!! interlink(["goto"=>"myBots@delConfirm",'tenant'=>$tenant->id])!!}"
            },
            {
                "text":"خیر",
                "callback_data":"{!! interlink(["goto"=>"myBots@show",'tenant'=>$tenant->id])!!}"
            }
        ]
    ]
}