<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
