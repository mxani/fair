{
    "inline_keyboard":[
@if(!empty ($flow))

    @if(isset($nextpic)||isset($prevpic))
        [
        @isset($nextpic)
            {
                "text":"تصویر بعدی",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$cat,"id"=>$flow,"pic"=>$nextpic])!!}"
            }
        @endisset
        @if(isset($nextpic)&&isset($prevpic))
        ,
        @endif
        @isset($prevpic)
            {
                "text":"تصویر قبلی",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$cat,"id"=>$flow,"pic"=>$prevpic])!!}"
            }
        @endisset
        ],
    @endif
    @if(isset($next)||isset($prev))
        [
        @isset($next)
            {
                "text":"محصول بعدی",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$cat,"id"=>$next])!!}"
            }
        @endisset
        @if(isset($next)&&isset($prev))
        ,
        @endif
        @isset($prev)
            {
                "text":"محصول قبلی",
                "callback_data":"{!! interlink(["goto"=>"Products@index","cat"=>$cat,"id"=>$prev])!!}"
            }
        @endisset
        ],
    @endif

        [
            {
                "text":"پیشنهاد به دوستان",
                "switch_inline_query":"Product-{{$flow}}"
            }
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