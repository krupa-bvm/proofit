<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'certificate_id', 'file_name', 'sha256_hash',
        'timestamp', 'blockchain_tx', 'project_name',
        'description', 'language', 'preview_url',
    ];
}
