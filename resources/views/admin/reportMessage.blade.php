@if(!empty($report))
    بازدیدکنندگان :
    ..................................................................

    🔸 امروز : <code>{{$report['today']}}</code> نفر

    🔸 دیروز : <code>{{$report['yesterday']}}</code> نفر

    🔸 هفته گذشته : <code>{{$report['lastweek']}}</code> نفر
  
   <a href="">&#8205;</a>

@endif