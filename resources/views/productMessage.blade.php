@if(!empty($product))

🔹<code>{{$product->title}}</code>

  👌قیمت :<code>{{$product->price}}</code>

{{$product->description}} ✌️😊
 
    @isset($product->files[$pic])
        <a href="{{$product->files[$pic]??''}}">&#8205;</a>
    @endisset

@endif 