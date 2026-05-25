<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Founder extends Model
{
    use HasFactory;

    protected $fillable = [
        'startup_id',
        'full_name',
        'gender',
        'age',
        'email',
        'phone',
        'education',
        'college',
        'iit_iim_nit',
        'serial_entrepreneur',
        'linkedin_profile',
        'experience_years',
        'prev_company',
    ];

    public function startup()
    {
        return $this->belongsTo(Startup::class);
    }
}