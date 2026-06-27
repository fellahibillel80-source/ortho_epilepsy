<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RehabResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'accuracy_percentage',
        'avg_reaction_time_ms',
        'total_errors',
        'missed_responses',
        'trials',
    ];

    protected $casts = [
        'trials' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(RehabAssignment::class, 'assignment_id');
    }
}
