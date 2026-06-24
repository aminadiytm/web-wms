<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrsMaster extends Model
{
    use HasFactory;

    protected $table = 'trs_mstr';
    protected $primaryKey = 'trs_id';

    protected $fillable = [
        'trs_code',
        'from_wh_id',
        'to_wh_id',
        'trs_stat',
        'trs_shipped',
        'trs_rcv',
        'trs_add_by',
        'trs_upd_by',
    ];

    public function from_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_wh_id', 'wh_id');
    }

    public function to_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_wh_id', 'wh_id');
    }
}
