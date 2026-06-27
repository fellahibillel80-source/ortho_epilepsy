<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'specialist_id',
        'test_id',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function test()
    {
        return $this->belongsTo(CognitiveTest::class, 'test_id');
    }

    public function result()
    {
        return $this->hasOne(TestResult::class, 'assignment_id');
    }
}
