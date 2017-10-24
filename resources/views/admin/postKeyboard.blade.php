{
    "inline_keyboard":[
    @if(isset($next) || isset($prev))
        
            [
                @isset($next)
                {
                    "text":"{!! $next_title !!}",
                    "callback_data":"{!! interlink(["goto"=>$goto,"id"=>$next,"postType"=>$postType])!!}"
                }
                @endisset
            ]

            @if(isset($next) && isset($prev))
            ,
            @endif
            @isset($prev)
            [
                {
                    "text":"{!! $prev_title !!}",
                    "callback_data":"{!! interlink(["goto"=>$goto,"id"=>$prev,"postType"=>$postType])!!}"
                }
            ]
            @endisset
            
        @endif
        ,
        [
            {
                "text":"📝 ویرایش توضیحات",
                "callback_data":"{!! interlink(["goto"=>'adminPosts@editContent',"id"=>$current_id,"postType"=>$postType])!!}"
            },
            {
                "text":"📝 ویرایش عنوان",
                "callback_data":"{!! interlink(["goto"=>'adminPosts@editTitle',"id"=>$current_id,"postType"=>$postType])!!}"
            }
        ]
        ,
        [
            {
                @if($postType == 'blog')
                "text":"❌ حذف مطلب",
                @else
                "text":"❌ حذف صفحه",
                @endif
                "callback_data":"{!! interlink(["goto"=>'adminPosts@destroy',"id"=>$current_id,"postType"=>$postType])!!}"
            },
            {
                "text":"📝 تغییر تصویر",
                "callback_data":"{!! interlink(["goto"=>'adminPosts@newPic',"id"=>$current_id,"postType"=>$postType])!!}"
            }
        ]
        ,
        [
            {
                @if($postType == 'blog')
                "text":"📄 افزودن مطلب جدید",
                @else
                "text":"📄 افزودن صفحه جدید",
                @endif
                "callback_data":"{!! interlink(["goto:adminPosts@newPost","postType"=>$postType])!!}"
            }
        ]
    ]
}