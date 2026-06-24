<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrsDet extends Model
{
    use HasFactory;

    protected $table = 'trsd_det';
    protected $primaryKey = 'trsd_id';

    protected $fillable = [
        'ref_trs_id',
        'ref_prd_id',
        'from_loc_id',
        'to_loc_id',
        'trsd_confrm_by',
        'trsd_qty',
        'trsd_add_by',
        'trsd_upd_by',
    ];
}
