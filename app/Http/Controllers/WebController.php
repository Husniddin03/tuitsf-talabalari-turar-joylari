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
            'fish' => 'required|string|max:255',
            'fakultet' => 'required|string|max:255',
            'guruh' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'tyutori' => 'nullable|string|max:255',
            'hudud' => 'nullable|string|max:255',
            'manzil' => 'nullable|string|max:255',
            'url_manzil' => 'nullable|url|max:255',
        ]);

        Student::create($validated);

        return response()->json(['message' => 'Talaba muvaffaqiyatli saqlandi!']);
    }

    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'fish' => 'required|string|max:255',
            'fakultet' => 'required|string|max:255',
            'guruh' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'tyutori' => 'nullable|string|max:255',
            'hudud' => 'nullable|string|max:255',
            'manzil' => 'nullable|string|max:255',
            'url_manzil' => 'nullable|url|max:255',
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
