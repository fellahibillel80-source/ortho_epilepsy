<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'time_of_day',
    ];

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }
}
