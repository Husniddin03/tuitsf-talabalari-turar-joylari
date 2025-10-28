<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index()
    {
        $students = Student::all();
        $users = User::all();

        return view('index', compact('students', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'talaba_id' => 'required|string|max:255',
            'fish' => 'required|string|max:255',
            'fakultet' => 'required|string|max:255',
            'guruh' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'tyutori' => 'nullable|string|max:255',
            'hudud' => 'nullable|string|max:255',
            'doimiy_yashash_viloyati' => 'nullable|string|max:255',
            'doimiy_yashash_tumani' => 'nullable|string|max:255',
            'doimiy_yashash_manzili' => 'nullable|string|max:255',
            'doimiy_yashash_manzili_urli' => 'nullable|string|max:255',
            'vaqtincha_yashash_viloyati' => 'nullable|string|max:255',
            'vaqtincha_yashash_tumani' => 'nullable|string|max:255',
            'vaqtincha_yashash_manzili' => 'nullable|string|max:255',
            'vaqtincha_yashash_manzili_urli' => 'nullable|string|max:255',
            'uy_egasi' => 'nullable|string|max:255',
            'uy_egasi_telefoni' => 'nullable|string|max:255',
            'yotoqxona_nomeri' => 'nullable|string|max:255',
            'narx' => 'nullable|string|max:255',
            'ota_ona' => 'nullable|string|max:255',
            'ota_ona_telefoni' => 'nullable|string|max:255',
        ]);

        Student::create($validated);

        return response()->json(['message' => 'Talaba muvaffaqiyatli saqlandi!']);
    }

    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'talaba_id' => 'required|string|max:255',
            'fish' => 'required|string|max:255',
            'fakultet' => 'required|string|max:255',
            'guruh' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'tyutori' => 'nullable|string|max:255',
            'hudud' => 'nullable|string|max:255',
            'doimiy_yashash_viloyati' => 'nullable|string|max:255',
            'doimiy_yashash_tumani' => 'nullable|string|max:255',
            'doimiy_yashash_manzili' => 'nullable|string|max:255',
            'doimiy_yashash_manzili_urli' => 'nullable|string|max:255',
            'vaqtincha_yashash_viloyati' => 'nullable|string|max:255',
            'vaqtincha_yashash_tumani' => 'nullable|string|max:255',
            'vaqtincha_yashash_manzili' => 'nullable|string|max:255',
            'vaqtincha_yashash_manzili_urli' => 'nullable|string|max:255',
            'uy_egasi' => 'nullable|string|max:255',
            'uy_egasi_telefoni' => 'nullable|string|max:255',
            'yotoqxona_nomeri' => 'nullable|string|max:255',
            'narx' => 'nullable|string|max:255',
            'ota_ona' => 'nullable|string|max:255',
            'ota_ona_telefoni' => 'nullable|string|max:255',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Talaba ma’lumotlari yangilandi!',
            'student' => $student
        ]);
    }

    // 🔴 Talabani o‘chirish
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json([
            'message' => 'Talaba muvaffaqiyatli o‘chirildi!'
        ]);
    }
}
