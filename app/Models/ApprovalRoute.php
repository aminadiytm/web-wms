<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRoute extends Model
{
    protected $primaryKey = 'approval_route_id';

    protected $fillable = [
        'route_code',
        'route_name',
        'transaction_type',
        'approver_user_id',
        'is_default',
        'is_active',
        'add_by',
        'upd_by',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id', 'id');
    }

}
