<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_name',
        'original_image',
        'reduced_image',
    ];

    /* Relación muchos a muchos*/

    public function purchase(){
        return $this->belongsToMany('App\Models\Purchase','product_purchase')
            ->withPivot('purchase_id')
            ->withTimestamps();
    }
}
