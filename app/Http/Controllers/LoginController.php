<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function checkAuth(Request $request)
    {
        // 1. Kiruvchi ma'lumotlarni tekshiramiz
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // 2. Foydalanuvchini topamiz
        $user = User::where('email', $validate['email'])->first();

        // 3. Agar foydalanuvchi topilmasa yoki parol noto‘g‘ri bo‘lsa
        if (!$user || !Hash::check($validate['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Email yoki parol noto‘g‘ri!',
            ]);
        }

        // 4. Rolini tekshiramiz
        if ($user->role !== 'admin') {
            return back()->withErrors([
                'email' => 'Faqat admin kirishi mumkin!',
            ]);
        }

        // 5. Auth orqali login qilish
        Auth::login($user);

        // 6. Muvaffaqiyatli javob
        return redirect('/')->with([
            'success' => 'Admin sifatida muvaffaqiyatli kirdingiz!',
            'user' => $user
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Siz tizimdan chiqdingiz!');
    }
}
