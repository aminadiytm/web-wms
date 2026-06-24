<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeMaster extends Model
{
    use HasFactory;

    protected $table = 'code_mstr';
    protected $primaryKey = 'doc_type';

    protected $fillable = [
        'doc_type',
        'period',
        'last_number',
    ];
}
