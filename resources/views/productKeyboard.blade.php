{
    "inline_keyboard":[
    @if(isset($next)||isset($prev))
        [
        @isset($next)
            {
                "text":"بعدی",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$cat,"id"=>$next])!!}"
            }
        @endisset
        @if(isset($next)&&isset($prev))
        ,
        @endif
        @isset($prev)
            {
                "text":"قبلی",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$cat,"id"=>$prev])!!}"
            }
        @endisset
        ],
    @endif
        [
            {
                "text":"نمایش دسته ها",
                "callback_data":"{{"goto:Categories@index"}}"
            }
        ]
    ]
}