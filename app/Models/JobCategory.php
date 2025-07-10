<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JobCategory extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];
    protected $fillable = ['category_id', 'category_name', 'category_description', 'category_image'];

    public function jobs()
    {
        return $this->hasMany(CompanyJob::class, 'category_id', 'job_category_id');
    }
}
