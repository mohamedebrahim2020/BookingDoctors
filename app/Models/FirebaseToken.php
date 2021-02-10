<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirebaseToken extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
    */
    protected $table = 'firebase_tokens';

    /**
     * Get the parent tokenable model (patient or doctor).
    */
    public function tokenable()
    {
        return $this->morphTo();
    } 
}
