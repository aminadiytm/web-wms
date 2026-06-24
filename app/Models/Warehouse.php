<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';
    protected $primaryKey = 'wh_id';

    protected $fillable = [
        'wh_code',
        'wh_name',
        'wh_addr',
        'wh_desc',
        'wh_add_by',
        'wh_upd_by'
    ];

    public function locations()
    {
        return $this->hasMany(Location::class, 'ref_wh_id', 'wh_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'ref_wh_id', 'wh_id');
    }

    public function inboundmstrs() 
    {
        return $this->hasMany(InboundMaster::class, 'ref_wh_id', 'wh_id');
    }

    public function outboundmstrs()
    {
        return $this->hasMany(OutboundMaster::class, 'ref_wh_id', 'wh_id');
    }

    public function from_trsmstrs()
    {
        return $this->hasMany(TrsMaster::class, 'from_wh_id', 'wh_id');
    }

    public function to_trsmstrs()
    {
        return $this->hasMany(TrsMaster::class, 'to_wh_id', 'wh_id');
    }
}
