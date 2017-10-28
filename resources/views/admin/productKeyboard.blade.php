{
    "inline_keyboard":[
@if(!empty ($flow))   
    @if(isset($nextpic)||isset($prevpic))
        [
        @isset($nextpic)
            {
                "text":"تصویر بعدی",
                "callback_data":"{!! interlink(["goto"=>$goto,"cat_id"=>$cat_id,"id"=>$flow,"pic"=>$nextpic])!!}"
            }
        @endisset
        @if(isset($nextpic)&&isset($prevpic))
        ,
        @endif
        @isset($prevpic)
            {
                "text":"تصویر قبلی",
                "callback_data":"{!! interlink(["goto"=>$goto,"cat_id"=>$cat_id,"id"=>$flow,"pic"=>$prevpic])!!}"
            }
        @endisset
        ],
    @endif
    @if(isset($next)||isset($prev))
        [
        @isset($next)
            {
                "text":"محصول بعدی",
                "callback_data":"{!! interlink(["goto"=>$goto,"cat_id"=>$cat_id,"id"=>$next])!!}"
            }
        @endisset
        @if(isset($next)&&isset($prev))
        ,
        @endif
        @isset($prev)
            {
                "text":"محصول قبلی",
                "callback_data":"{!! interlink(["goto"=>$goto,"cat_id"=>$cat_id,"id"=>$prev])!!}"
            }
        @endisset
        ],
    @endif  
   
        [ 
            {
                "text":"➕ افزودن تصویر جدید",
                "callback_data":"{!! interlink(["goto"=>'adminProducts@newPic',"cat_id"=>$cat_id,"id"=>$flow])!!}"
            }
           @if(!is_null($pic))
            ,
            {
                "text":"❌ حذف تصویر فعلی",
                "callback_data":"{!! interlink(["goto"=>'adminProducts@removePic',"cat_id"=>$cat_id,"id"=>$flow,'pic'=>$pic])!!}"
            }
           @endif
        ],

        [
            {
                "text":"📝 ویرایش توضیحات",
                "callback_data":"{!! interlink(["goto"=>'adminProducts@editContent',"id"=>$flow,"cat_id"=>$cat_id])!!}"
            },
            {
                "text":"📝 ویرایش عنوان",
                "callback_data":"{!! interlink(["goto"=>'adminProducts@editTitle',"id"=>$flow,"cat_id"=>$cat_id])!!}"
            }
        ],
        [
            {
                "text":"❌ حذف محصول",
                "callback_data":"{!! interlink(["goto"=>'adminProducts@destroy',"id"=>$flow,"cat_id"=>$cat_id])!!}"
            },
            {
                "text":"📝 ویرایش قیمت",
                "callback_data":"{!! interlink(["goto"=>'adminProducts@editPrice',"id"=>$flow,"cat_id"=>$cat_id])!!}"
            }
        ],
@endif
        [
            {
                "text":"📦 افزودن محصول جدید",
                "callback_data":"{!! interlink(["goto:adminProducts@newProduct","cat_id"=>$cat_id])!!}"
            }
        ],
        [
            {
                "text":"بازگشت به دسته ها",
                "callback_data":"{{"goto:adminProducts@showCats"}}"
            }
        ]
    ] 
}