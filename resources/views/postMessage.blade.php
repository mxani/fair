@if(!empty($post))

    @if(!empty($post->thumb))
        <a href="{{$post->thumb}}">&#8205;</a>
    @endif

    @if(!empty(config("XBtelegram.bot-username")))
        <a href="http://t.me/{{config("XBtelegram.bot-username")}}">{{$post->title}}</a>
    @else
        <code>{{$post->title}}</code>
    @endif

    {{$post->content}}
  
    @if(!empty($post->thumb))
        <a href="{{$post->thumb}}">&#8205;</a>
    @endif



@else
    با عرض پوزش موردی یافت نشد!
@endif