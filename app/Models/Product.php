<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'prd_id';

    protected $fillable = [
        'prd_code',
        'prd_name',
        'prd_unit',
        'ref_cat_id',
        'prd_min_stock',
        'prd_desc',
        'prd_add_by',
        'prd_upd_by',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'ref_cat_id', 'cat_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'ref_prd_id', 'prd_id');
    }

    public function inbounddets()
    {
        return $this->hasMany(InboundDet::class, 'ref_prd_id', 'prd_id');
    }

    public function outbounddets()
    {
        return $this->hasMany(OutboundDet::class, 'ref_prd_id', 'prd_id');
    }
}
