<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'title',  'description','files', 'price', 'status' ];
	protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
	protected $hidden = [ 'updated_at', 'deleted_at' ];
	protected $casts = [
		'files'=>'array'
	];

	public function category() {
		return $this->belongsToMany( Category::class, 'product_categories', 'product_id', 'cat_id' )->withTimestamps();
	}

    public function Orders(){
        return $this->hasMany(Master\Order::class);
    }

    public function Persons(){
        return $this->belongsToMany(Person::class,'orders')->
        withPivot('detail','status','paid_at');
    }
	
	public function Tenants(){
		return $this->hasManyThrough(Master\Tenant::class,Master\Order::class);
	}

}
