<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;
    protected $table = 'area';

    protected $fillable = [
        'area_name', 'created_at', 'updated_at', 'deleted_at'
    ];
}
