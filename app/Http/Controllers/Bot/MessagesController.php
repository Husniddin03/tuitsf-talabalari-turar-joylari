<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class MessagesController extends Controller
{
    public function controll($update)
    {
        if ($update->message->text == '/start') {
            $this->start($update->message);
        } elseif (strpos($update->message->text, '/student') === 0) {
            $studentId = (int)str_replace('/student', '', $update->message->text);
            $this->student($update->message->chat->id, $studentId);
        } else {
            $message = Telegram::sendMessage([
                'chat_id' => $update->message->chat->id,
                'text' => "⌛ *Qidirilmoqda...*",
                'parse_mode' => 'Markdown',
            ]);

            $this->search($update->message->chat->id, $update->message->text);

            Telegram::deleteMessage([
                'chat_id' => $update->message->chat->id,
                'message_id' => $message->getMessageId(),
            ]);
        }
    }

    public function search($chatId, $query)
    {
        // 🔍 Talabani bir nechta ustunlar bo‘yicha qidiramiz
        $students = Student::where('fish', 'LIKE', "%{$query}%")
            ->orWhere('fakultet', 'LIKE', "%{$query}%")
            ->orWhere('telefon', 'LIKE', "%{$query}%")
            ->orWhere('guruh', 'LIKE', "%{$query}%")
            ->orWhere('tyutori', 'LIKE', "%{$query}%")
            ->orWhere('hudud', 'LIKE', "%{$query}%")
            ->get();

        // 🔸 Agar hech kim topilmasa
        if ($students->isEmpty()) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Talaba topilmadi.\n\nIltimos, boshqa ma’lumot kiriting.",
            ]);
            return;
        }

        // 🔸 Topilgan talabalarni ko‘rsatish
        $count = $students->count();
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "✅ {$count} ta talaba topildi:",
        ]);

        // 🔸 Har bir topilgan talaba uchun alohida funksiya chaqiramiz
        foreach ($students as $student) {
            $this->student($chatId, $student->id);
        }
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "✅ {$count} ta talaba topildi:",
        ]);
    }


    public function start($message)
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => "Barcha talabalar", 'callback_data' => "all_students_1"]),
                Keyboard::inlineButton(['text' => "Qidirish", 'callback_data' => "search"]),
                Keyboard::inlineButton(['text' => "Excel yuklash", 'callback_data' => "download"])
            ]);
        Telegram::sendPhoto([
            'chat_id' => $message->chat->id,
            'photo' => InputFile::create(public_path('images/tatusf.png')),
            'caption' => "📘 Muhammad Al-Xorazmiy nomidagi Toshkent Axborot Texnologiyalari Universiteti Samarqand filiali talabalarining turar joylari haqida ma'lumot beruvchi botga xush kelibsiz!",
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }

    public function student($chatId, $studentId)
    {
        $student = Student::find($studentId);

        if (!$student) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Student topilmadi."
            ]);
            return;
        }

        $text = "👤 *Student Ma'lumotlari*\n\n";
        $text .= "📝 F.I.SH: {$student->fish}\n";
        $text .= "🎓 Fakultet: {$student->fakultet}\n";
        $text .= "👥 Guruh: {$student->guruh}\n";
        $text .= "📞 Telefon: {$student->telefon}\n";
        $text .= "👨‍🏫 Tyutori: {$student->tyutori}\n";
        $text .= "🌍 Hudud: {$student->hudud}\n";
        $text .= "📍 Manzil: {$student->manzil}\n";

        if (!empty($student->url_manzil)) {
            $text .= "🗺 [Xaritada ko'rish]({$student->url_manzil})\n";
        }

        $keyboard = [
            [
                [
                    'text' => '🔙 Orqaga',
                    'callback_data' => 'start'
                ]
            ]
        ];

        $reply_markup = Keyboard::make([
            'inline_keyboard' => $keyboard
        ]);

        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => $reply_markup
        ]);
    }
}
