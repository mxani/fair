<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;
	use \App\Helpers\TriRow;

	protected $fillable = [ 'title',  'description','files', 'price', 'status' ];
	protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
	protected $hidden = [ 'updated_at', 'deleted_at' ];
	protected $casts = [
		'files'=>'array'
	];

	public function category() {
		return $this->belongsToMany( Category::class, 'product_categories', 'product_id', 'cat_id' )->withTimestamps();
	}
}
