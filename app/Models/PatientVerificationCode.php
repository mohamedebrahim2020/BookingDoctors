<?php

namespace App\Models;

use App\Filters\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientVerificationCode extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    /**
     * The table associated with the model.
     *
     * @var string
    */
    protected $table = 'verification_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'expired_at', 'patient_id',
    ];
}
