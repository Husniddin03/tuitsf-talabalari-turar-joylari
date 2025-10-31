<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentsVerifiy extends Model
{
    use HasFactory;

    protected $table = 'students_verifiy';

    protected $fillable = [
        'student_id',
        'password',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
