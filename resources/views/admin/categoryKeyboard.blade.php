{
    "inline_keyboard":[
        @if(empty($mode))

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
                        "callback_data":"{!! interlink(["goto"=>$goto,"cat_id"=>$items[$i]['id']])!!}"
                    },
                    {
                        "text":"{{$items[$i+1]['name']}}",
                        "callback_data":"{!! interlink(["goto"=>$goto,"cat_id"=>$items[$i+1]['id']])!!}"
                    }
                ]
            @endfor
            @isset($enditem)
                ,[
                    {
                        "text":"{{$enditem['name']}}",
                        "callback_data":"{!! interlink(["goto"=>$goto,"cat_id"=>$enditem['id']])!!}"
                    }
                ]
            @endisset
            @if(strpos($goto,'adminCategories')!==false)
                ,
                [
                    {
                        "text":"➕ ایجاد دسته جدید",
                        "callback_data":"{!! interlink(["goto"=>"adminCategories@add"])!!}"
                    }
                ]
            @endif

        @else
        
            [
                @if($category!=1)
                {
                    "text":"❌ حذف",
                    "callback_data":"{!! interlink(["goto"=>"adminCategories@destroy","cat_id"=>$category])!!}"
                },
                @endif
                {
                    "text":"📝 ویرایش",
                    "callback_data":"{!! interlink(["goto"=>"adminCategories@edit","cat_id"=>$category])!!}"
                }
            ]
            @if(isset($backToList))
            ,
            [
                {
                    "text":"بازگشت به دسته ها",
                    "callback_data":"{!! interlink(["goto"=>"adminCategories@index"])!!}"
                }
            ]
            @endif

        @endif  
    ]
}