<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundDet extends Model
{
    use HasFactory;

    protected $table = 'inbd_det';
    protected $primaryKey = 'inbd_id';

    protected $fillable = [
        'ref_inb_id',
        'ref_prd_id',
        'ref_loc_id',
        'qty_order',
        'qty_rcv',
        'inbd_add_by',
        'inbd_upd_by',
    ];

    public function inbound()
    {
        return $this->belongsTo(InboundMaster::class, 'ref_inb_id', 'inb_id');
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
