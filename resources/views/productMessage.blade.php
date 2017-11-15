@if(!empty($product))

🔹<code>{{$product->title}}</code>
@if(!empty($product->price))

  💰 قیمت :<code>{{$product->price}}</code> تومان
@endif

{{$product->description}}
 
    @isset($product->files[$pic])
        <a href="{{$product->files[$pic]??''}}">&#8205;</a>
    @endisset

@endif 