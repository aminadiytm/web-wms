<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';
    protected $primaryKey = 'st_id';

    protected $fillable = [
        'ref_prd_id',
        'ref_wh_id',
        'ref_loc_id',
        'qty_on_hand',
        'qty_reserved',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ref_prd_id', 'prd_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'ref_wh_id', 'wh_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'ref_loc_id', 'loc_id');
    }

}
