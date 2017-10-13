{
    "inline_keyboard":[
    @if(isset($next)||isset($prev))
        [
        @isset($next)
            {
                "text":"بعدی",
                "callback_data":"{!! addslashes(json_encode([
                    "goto"=>"Products@index","cat"=>$cat,"id"=>$next]))!!}"
            }
        @endisset
        @if(isset($next)&&isset($prev))
        ,
        @endif
        @isset($prev)
            {
                "text":"قبلی",
                "callback_data":"{!! addslashes(json_encode([
                    "goto"=>"Products@index","cat"=>$cat,"id"=>$prev]))!!}"
            }
        @endisset
        ],
    @endif
        [
            {
                "text":"نمایش دسته ها",
                "callback_data":"{!! addslashes('{"goto":"Categories@index"}') !!}"
            }
        ]
    ]
}