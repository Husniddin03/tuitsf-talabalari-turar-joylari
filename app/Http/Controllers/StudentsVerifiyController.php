<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentsVerifiy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentsVerifiyController extends Controller
{
    public function index()
    {
        $student = Student::find(session('student_id'));
        return view('verifiy.index', compact('student'));
    }

    public function login()
    {
        return view('verifiy.login');
    }

    public function logout(Request $request) {
        $request->session()->forget('student_id');
        return redirect()->route('verifiy.login')->with('success', "Muoffaqiyatli chiqdingiz");
    }

    public function chekLogin(Request $request)
    {
        $request->validate([
            'talaba_id' => 'required|digits:12',
            'password' => 'nullable|string|min:6',
        ]);

        $student = Student::where('talaba_id', $request->talaba_id)->first();

        if (!$student) {
            return back()->with('error', "Siz tizimda mavjud emassiz!");
        }

        $verifiy = $student->verifiy;

        if (!$verifiy || !$verifiy->password) {
            session(['student_id' => $student->id]);
            return redirect('verifiy/index');
        }

        if (!$request->filled('password')) {
            return back()->with('talaba_id', $request->talaba_id);
        }

        if (Hash::check($request->password, $verifiy->password)) {
            session(['student_id' => $student->id]);
            return redirect('verifiy/index');
        } else {
            return back()->with('talaba_id', $request->talaba_id)
                ->with('error', 'Parol noto‘g‘ri!');
        }
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
        if(isset($validated['yotoqxona_nomeri'])){
            $validated['vaqtincha_yashash_viloyati'] = null;
            $validated['vaqtincha_yashash_tumani'] = null;
            $validated['vaqtincha_yashash_manzili'] = null;
            $validated['vaqtincha_yashash_manzili_urli'] = null;
        }else{
            $validated['yotoqxona_nomeri'] = null;
        }


        $student->update($validated);

        return redirect('verifiy/index')->with('success', 'Talaba maʼlumotlari yangilandi.');
    }

    public function newPassword(Request $request, string $id) {
        $validated = $request->validate([
            'password' => 'required|min:6|confirmed', 
        ]);

        $student = StudentsVerifiy::where('student_id', $id)->first();
        $validated['password'] = Hash::make($request->password);
        if($student){
            $student->update($validated);
        }else{
            StudentsVerifiy::create([
                'student_id' => $id,
                'password' => $validated['password']
            ]);
        }
        return back()->with('success', 'Parolingiz saqlandi!');
    }
}
