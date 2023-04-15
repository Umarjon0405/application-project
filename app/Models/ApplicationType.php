<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationType extends Model
{
    use HasFactory;

    protected $table = 'application_types';

    protected $fillable = [
        'title',
        'active',
        'category_id'
    ];

    public function category(){
        return $this->hasOne(ApplicationCategory::class, 'id', 'category_id');
    }

    public $timestamps = false;
}
