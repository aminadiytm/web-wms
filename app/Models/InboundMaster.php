<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundMaster extends Model
{
    use HasFactory;

    protected $table = 'inb_mstr';
    protected $primaryKey = 'inb_id';

    protected $fillable = [
        'inb_code',
        'ref_wh_id',
        'inb_supplier',
        'inb_stat',
        'inb_rcv',
        'inb_add_by',
        'inb_upd_by',
    ];

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'ref_wh_id', 'wh_id');
    }

    public function inbounddets()
    {
        return $this->hasMany(InboundDet::class, 'ref_inb_id', 'inb_id');
    }
}
