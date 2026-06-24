<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Translation\Extractor\Visitor\TransMethodVisitor;

class OutboundMaster extends Model
{
    use HasFactory;

    protected $table = 'outb_mstr';
    protected $primaryKey = 'outb_id';

    protected $fillable = [
        'outb_code',
        'ref_wh_id',
        'outb_customer',
        'outb_stat',
        'outb_shipped',
        'outb_add_by',
        'outb_upd_by',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'ref_wh_id', 'wh_id');
    }
    
    public function outbounddets()
    {
        return $this->hasMany(OutboundDet::class, 'ref_outb_id', 'outb_id');
    }

}
