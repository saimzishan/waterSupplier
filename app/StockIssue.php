<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockIssue extends Model
{
    use SoftDeletes;
    protected $table = 'stockissue';

    protected $fillable = [
        'quantity', 'salesmen_id', 'stock_id', 'created_at', 'updated_at', 'deleted_at'
    ];
}
