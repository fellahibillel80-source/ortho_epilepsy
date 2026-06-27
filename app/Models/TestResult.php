<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'patient_id',
        'test_id',
        'score',
        'duration_seconds',
        'errors_count',
        'raw_details',
    ];

    protected $casts = [
        'raw_details' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(TestAssignment::class, 'assignment_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function test()
    {
        return $this->belongsTo(CognitiveTest::class, 'test_id');
    }
}
