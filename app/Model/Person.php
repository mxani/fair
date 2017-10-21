<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'telegramID','detail','type','status'];
    protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
    protected $hidden = [ 'updated_at', 'deleted_at' ];
    protected $casts = ['detail' => 'array'];

    public function Orders(){
        return $this->hasMany(Master\Order::class);
    }

    public function Products(){
        return $this->belongsToMany(Product::class,'orders')->
        withPivot('detail','status','paid_at');
    }

    public function Tenants(){
        return $this->hasManyThrough(Master\Tenant::class,Master\Order::class);
    }

}
