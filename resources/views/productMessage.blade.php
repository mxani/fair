@if(!empty($product))
    Title: <b>{{$product->title}}</b>

    Price : <b>{{$product->price}}</b>

    <b>Description :</b>
    <pre>{{$product->description}}</pre>

    <?php
    //foreach($product->files as $file)
    //endforeach
    ?>
    
    <b>product_image</b>
    @isset($product->files[$pic])
    <a href="{{$product->files[$pic]}}">&#8205;</a>
    @endisset

@else
    empty
@endif