<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $primaryKey = 'approval_log_id';

    protected $fillable = [
        'approval_id',
        'action',
        'user_id',
        'notes',
    ];
}
