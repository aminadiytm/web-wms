<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    protected $primaryKey = 'loc_id';

    protected $fillable = [
        'ref_wh_id',
        'loc_code',
        'loc_desc',
        'loc_act',
        'loc_add_by',
        'loc_upd_by'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'ref_wh_id', 'wh_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'ref_loc_id', 'loc_id');
    }

    public function inbounddets()
    {
        return $this->hasMany(InboundDet::class, 'ref_loc_id', 'loc_id');
    }

    public function outbounddets()
    {
        return $this->hasMany(OutboundDet::class, 'ref_loc_id', 'loc_id');
    }
}
