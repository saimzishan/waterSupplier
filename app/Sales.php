<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use SoftDeletes;
    protected $table = 'sales';

    protected $fillable = [
        'quantity', 'salesmen_id', 'stock_id', 'created_at', 'updated_at', 'deleted_at'
    ];
}
