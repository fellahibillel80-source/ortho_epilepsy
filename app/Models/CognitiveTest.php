<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CognitiveTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'executive_function',
    ];

    public function assignments()
    {
        return $this->hasMany(TestAssignment::class, 'test_id');
    }

    public function results()
    {
        return $this->hasMany(TestResult::class, 'test_id');
    }
}
