<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
    ];
}
