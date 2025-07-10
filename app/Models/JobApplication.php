<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'applicant_resume',
        'applicant_cover_letter',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(CompanyJob::class, 'job_id', 'job_id');
    }
}
