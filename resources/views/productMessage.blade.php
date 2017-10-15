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
    <a href="{{$product->files[$pic]}}">&#8205;</a>


@else
    empty
@endif