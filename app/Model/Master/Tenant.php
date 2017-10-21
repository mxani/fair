<?php

namespace App\Model\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'person_id','product_id','price'];
    protected $dates = [ 'paid_at','created_at', 'updated_at', 'deleted_at' ];
    protected $hidden = [ 'updated_at', 'deleted_at' ];
    protected $casts = ['detail' => 'array'];

    public function Order(){
        return $this->belongsTo(Order::class);
    }
    
}
