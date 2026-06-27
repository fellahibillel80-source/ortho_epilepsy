<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialist_id',
        'type',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'category_or_function',
        'status', // 'pending', 'approved', 'rejected'
    ];

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }
}
