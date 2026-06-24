<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutboundDet extends Model
{
    use HasFactory;

    protected $table = 'outbd_det';
    protected $primaryKey = 'outbd_id';

    protected $fillable = [
        'ref_outb_id',
        'ref_prd_id',
        'ref_loc_id',
        'outbd_confrm_by',
        'qty_req',
        'qty_picked',
        'outbd_add_by',
        'outbd_upd_by'
    ];

    public function outbound()
    {
        return $this->belongsTo(OutboundMaster::class, 'ref_outb_id', 'outb_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'ref_prd_id', 'prd_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'ref_loc_id', 'loc_id');
    }
}
