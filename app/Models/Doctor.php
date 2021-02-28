<?php

namespace App\Models;

use App\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class Doctor extends Authenticatable
{
    use HasFactory, Notifiable, Filterable, HasApiTokens, SoftDeletes;
  
     /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'name', 'email', 'password', 'phone','specialization_id', 'gender'
        , 'photo', 'degree_copy', 'activated_at', 'average_reviews'
    ];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function workingDays()
    {
        return $this->hasMany(DoctorWorkingDays::class, 'doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get all of the patient`s firebase tokens.
    */
    public function firebaseTokens()
    {
        return $this->morphMany(FirebaseToken::class, 'user');
    }

    /**
     * Get all of the reviews for the project.
    */
    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Appointment::class);
    }
}
