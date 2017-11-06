@if(!empty($post))

<code>{{$post->title}}</code>

{{$post->content}}
  
@if(!empty($post->thumb))
<a href="{{$post->thumb}}">&#8205;</a>
@endif

@endif