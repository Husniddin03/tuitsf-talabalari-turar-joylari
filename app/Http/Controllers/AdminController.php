<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Yangi foydalanuvchi yaratish
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'chat_id' => 'required|numeric|unique:users',
            'role' => 'nullable|string|in:user,admin,superadmin',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $validated['role'] ?? 'user';

        $user = User::create($validated);

        return response()->json([
            'message' => 'Foydalanuvchi muvaffaqiyatli yaratildi!',
            'user' => $user
        ]);
    }

    /**
     * Foydalanuvchi ma’lumotlarini yangilash
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'chat_id' => 'sometimes|numeric|unique:users,chat_id,' . $user->id,
            'role' => 'sometimes|string|in:user,admin,superadmin',
            'password' => 'nullable|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Foydalanuvchi ma’lumotlari yangilandi!',
            'user' => $user
        ]);
    }

    /**
     * Foydalanuvchini o‘chirish
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Foydalanuvchi muvaffaqiyatli o‘chirildi!'
        ]);
    }
}
