<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToSchool;

class Student extends Model
{
    use HasFactory, BelongsToOrganization, BelongsToSchool;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'organization_id',
        'student_id_number',
        'campus',
        'lname',
        'fname',
        'mname',
        'ext',
        'gender',
        'course',
        'yearlevel',
        'section',
    ];

    /**
     * Get the user account associated with this student.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'email', 'student_id_number');
    }
}
