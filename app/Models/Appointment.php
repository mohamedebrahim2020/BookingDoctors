<?php

namespace App\Models;

use App\Filters\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'time', 'duration', 'patient_id', 'status', 'cancel_reason'
    ];

    protected $casts = [
        'time' => 'timestamp'
    ];

    public function setTimeAttribute($value)
    {
        if ($value) {
            $this->attributes['time'] = Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s');
        }    
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'appointment_id');
    }
}
