<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'talaba_id',
        'fish',
        'fakultet',
        'guruh',
        'telefon',
        'tyutori',
        'hudud',
        'doimiy_yashash_viloyati',
        'doimiy_yashash_tumani',
        'doimiy_yashash_manzili',
        'doimiy_yashash_manzili_urli',
        'vaqtincha_yashash_viloyati',
        'vaqtincha_yashash_tumani',
        'vaqtincha_yashash_manzili',
        'vaqtincha_yashash_manzili_urli',
        'uy_egasi',
        'uy_egasi_telefoni',
        'yotoqxona_nomeri',
        'narx',
        'ota_ona',
        'ota_ona_telefoni',
    ];

    public function verifiy()
    {
        return $this->hasOne(StudentsVerifiy::class, 'student_id');
    }
}
