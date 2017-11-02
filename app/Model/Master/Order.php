<?php

namespace App\Model\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'person_id','product_id','price'];
    protected $dates = [ 'paid_at','created_at', 'updated_at', 'deleted_at' ];
    protected $hidden = [ 'updated_at', 'deleted_at' ];
    protected $casts = ['detail' => 'array'];

    public function Person(){
        return $this->belongsTo(\App\Model\Person::class);
    }
    
    public function Product(){
        return $this->belongsTo(\App\Model\Product::class);
    }

    public function Tenant(){
        return $this->hasOne(\App\Model\Master\Tenant::class);
    }
    
}
