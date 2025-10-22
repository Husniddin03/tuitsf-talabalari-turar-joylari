<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Bot\CallbackController;
use App\Http\Controllers\Bot\MessagesController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    public function handle(Request $request)
    {
        $update = Telegram::getWebhookUpdate();

        if ($update->isType('callback_query')) {
            if ($this->userCheck($update->callbackQuery->message->chat->id)) {
                (new CallbackController)->controll($update);
            } else {
                $this->check($update->callbackQuery->message->chat->id);
            }
        } elseif ($update->isType('message')) {
            $chatId = $update->message->chat->id;
            $text = trim($update->message->text);

            if ($this->userCheck($chatId)) {
                (new MessagesController)->controll($update);
            } elseif ($text != '/start') {
                $this->login($update->message);
            } else {
                $this->check($chatId);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    // 🔹 Foydalanuvchi adminmi, tekshirish
    public function userCheck($chatId)
    {
        $user = User::where('chat_id', $chatId)->first();

        if ($user) {
            return $user->role === 'admin';
        } else {
            // Yangi foydalanuvchini ro‘yxatdan o‘tkazamiz
            User::create([
                'name' => 'Foydalanuvchi',
                'email' => 'user' . $chatId . '@example.com',
                'password' => Hash::make('default'),
                'chat_id' => $chatId, // 🔥 shu qator muhim
                'role' => 'user',
            ]);


            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "🆕 Siz tizimda ro‘yxatdan o‘tdingiz.\n\nIltimos, agar admin bo‘lsangiz login va parolingizni yuboring.\n\nFormat:\n`login parol`",
                'parse_mode' => 'Markdown',
            ]);

            return false;
        }
    }

    // 🔹 Agar admin bo‘lsa login orqali tekshirish
    public function login($message)
    {
        $chatId = $message->chat->id;
        $text = trim($message->text);
        $parts = explode(' ', $text);

        if (count($parts) === 2) {
            [$email, $password] = $parts;

            $user = User::where('email', $email)->first();

            if ($user && Hash::check($password, $user->password)) {
                User::where('chat_id', $chatId)->update(['role' => 'admin']);

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ Admin sifatida tizimga kirdingiz. /start",
                ]);
            } else {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ Login yoki parol noto‘g‘ri.",
                ]);
            }
        } else {
            $this->check($chatId);
        }
    }

    // 🔹 Admin bo‘lmagan foydalanuvchilarga xabar
    public function check($chatId)
    {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "⚠️ Bu botni faqat adminlar boshqarishi mumkin.\n\nIltimos, login va parolingizni yuboring.\n\nFormat:\n`login parol`",
            'parse_mode' => 'Markdown',
        ]);
    }
}
