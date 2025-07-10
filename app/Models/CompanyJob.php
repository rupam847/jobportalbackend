<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CompanyJob extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'job_id',
        'user_id',
        'job_title',
        'job_description',
        'job_category_id',
        'job_location',
        'job_city',
        'job_state',
        'job_country',
        'job_zip_code',
        'job_status',
        'job_salary',
    ];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id', 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id', 'job_id');
    }
}
