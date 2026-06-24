<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'cat_id';

    protected $fillable = [
        'cat_name',
        'cat_desc',
        'cat_add_by',
        'cat_upd_by',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'ref_cat_id', 'cat_id');
    }
}
