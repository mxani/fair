<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'text' ];
    protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];
    protected $hidden = [ 'updated_at', 'deleted_at' ];
}
