========================================
    <code>{{$tenant->detail['first_name']}}</code>
----------------------------------------
    @php
        $diff=\Carbon\Carbon::now()->diff($tenant->expires_at);
    @endphp
      وضعیت:  <code>{{$tenant->status}}</code> {{$tenant->status=='trial'?'پرداخت نشده':''}}
      تاریخ اعتبار: <i>{{$tenant->expires_at}}</i>
      باقیمانده اعتبار: {{$diff->days}} روز {{$diff->h}} ساعت {{$diff->i}} دقیقه <code>{{$diff->invert==0?'مانده':'تمام شده'}}</code>
      ----------------------------------
      نوع ربات: {{$tenant->order->product->title}}
      توضیحات: <pre>{{$tenant->order->product->description}}</pre>
      قیمت: {{$tenant->order->price}}
----------------------------------------