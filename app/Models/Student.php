<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use BelongsToOrganization, BelongsToSchool, HasFactory;

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
