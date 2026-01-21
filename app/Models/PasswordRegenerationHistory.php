<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordRegenerationHistory extends Model
{
    use HasFactory;

    protected $table = 'password_regeneration_history';

    protected $fillable = [
        'user_id',
        'student_id',
        'regenerated_at',
        'regenerated_by',
    ];

    protected $casts = [
        'regenerated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this history record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
