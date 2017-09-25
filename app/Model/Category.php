<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
	protected $fillable = [ 'name', 'parent_id' ];
	protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
	protected $hidden = [ 'created_at', 'updated_at', 'deleted_at' ];

	public function products() {
		return $this->belongsToMany( Product::class, 'product_categories', 'cat_id', 'product_id' )->withTimestamps();
	}

	public function Parent() {
		return $this->belongsTo( Category::class , 'parent_id', 'id' );
	}

	public function Childs() {
		return $this->hasMany( Category::class, 'parent_id', 'id' );
	}


	public function GetChilds( &$childs = [ ] ) {
		$this->Childs->each( function ( $item ) use ( &$childs ) {
			$childs[] = $item->id;
			$item->GetChilds( $childs );
		} );

		return $childs;
	}

	public function getParent( &$parents = [ ] ) {
		$this->Parent()->get()->each( function ( $item ) use ( &$parents ) {
			$parents[] = $item->id;
			$item->getParent( $parents );
		} );

		return $parents;
	}

	public function DeleteChilds() {
		return $this->Childs->each( function ( $item ) {
			$item->DeleteChilds();
			$item->delete();
		} );

		return true;
	}

}
