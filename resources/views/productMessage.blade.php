@if(!empty($product))

    <b>{{$product->title}}</b>

    <b>{{$product->price}}</b>

    <pre>{{$product->description}}</pre>
 
    @isset($product->files[$pic])
        <a href="{{$product->files[$pic]}}">&#8205;</a>
    @endisset

@endif