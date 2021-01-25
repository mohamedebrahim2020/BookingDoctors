<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorWorkingDays extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
    */
    protected $table = 'working_days';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'day', 'from', 'to', 'is_all_day', 'doctor_id',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
