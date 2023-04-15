<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'active',
        'created_at',
        'updated_at'
    ];

    public function types(){
        return $this->hasMany(ApplicationType::class, 'category_id', 'id');
    }
    public function type_count(){
        return $this->hasOne(ApplicationType::class, 'category_id', 'id')->groupBy('category_id')->select('category_id')->selectRaw("count(*)");
    }
}
