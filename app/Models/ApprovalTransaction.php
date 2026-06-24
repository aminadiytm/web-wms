<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalTransaction extends Model
{
    protected $primaryKey = 'approval_id';

    protected $fillable = [
        'transaction_type',
        'transaction_id',
        'transaction_code',
        'approval_route_id',
        'approver_user_id',
        'status',
        'token',
        'expired_at',
        'approved_at',
        'rejected_at',
        'remarks',
        'wa_target',
        'wa_sent_at',
        'created_by',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'wa_sent_at' => 'datetime',
    ];

    public function route()
    {
        return $this->belongsTo(ApprovalRoute::class, 'approval_route_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function logs()
    {
        return $this->hasMany(ApprovalLog::class, 'approval_id');
    }
}
