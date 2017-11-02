{
    "inline_keyboard":[
        [
            {
                "text":"تمدید 1 ماهه",
                "callback_data":"{!! interlink(["goto"=>"myBots@pay",'tenant'=>$tenant->id,'extend'=>'1'])!!}"
            },
            {
                "text":"تمدید 1 ساله",
                "callback_data":"{!! interlink(["goto"=>"myBots@pay",'tenant'=>$tenant->id,'extend'=>'12'])!!}"
            }
        ],
        [
            {
                "text":"تغییر توکن",
                "callback_data":"{!! interlink(["goto"=>"myBots@token",'tenant'=>$tenant->id])!!}"
            },
            {
                "text":"انصراف و برگشت هزینه",
                "callback_data":"{!! interlink(["goto"=>"myBots@cancel"])!!}"
            }
        ],
        [
            {
                "text":"حذف",
                "callback_data":"{!! interlink(["goto"=>"myBots@del",'tenant'=>$tenant->id])!!}"
            },
            {
                "text":"بازگشت",
                "callback_data":"{!! interlink(["goto"=>"myBots"])!!}"
            }
        ]
    ]
}