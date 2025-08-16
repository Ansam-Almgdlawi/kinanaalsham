<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectVolunteer extends Model
{
    use HasFactory;

    protected $table = 'project_volunteer';

    protected $fillable = [
        'project_id',
        'user_id',
        'user_type',
        'status',
        'motivation'
    ];
}
