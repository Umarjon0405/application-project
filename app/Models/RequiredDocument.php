<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'key',
        'application_type_id',
        'required',
        'multiple'
    ];

    protected $table = 'required_documents';
}
