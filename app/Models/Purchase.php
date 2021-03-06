<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'amount',
        'is_paid',
    ];

    /* Relación muchos a muchos*/

    public function product(){
        return $this->belongsToMany('App\Models\Product','product_purchase')
            ->withPivot('product_id')
            ->withTimestamps();
    }
}
