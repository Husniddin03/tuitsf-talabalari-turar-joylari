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
    // 🔍 Talabani bir nechta ustunlar bo'yicha qidiramiz
    $students = Student::where('fish', 'LIKE', "%{$query}%")
        ->orWhere('talaba_id', 'LIKE', "%{$query}%")
        ->orWhere('fakultet', 'LIKE', "%{$query}%")
        ->orWhere('telefon', 'LIKE', "%{$query}%")
        ->orWhere('guruh', 'LIKE', "%{$query}%")
        ->orWhere('tyutori', 'LIKE', "%{$query}%")
        ->orWhere('hudud', 'LIKE', "%{$query}%")
        ->orWhere('doimiy_yashash_viloyati', 'LIKE', "%{$query}%")
        ->orWhere('doimiy_yashash_tumani', 'LIKE', "%{$query}%")
        ->orWhere('vaqtincha_yashash_viloyati', 'LIKE', "%{$query}%")
        ->orWhere('vaqtincha_yashash_tumani', 'LIKE', "%{$query}%")
        ->orWhere('uy_egasi', 'LIKE', "%{$query}%")
        ->orWhere('yotoqxona_nomeri', 'LIKE', "%{$query}%")
        ->orWhere('ota_ona', 'LIKE', "%{$query}%")
        ->get();

    // 🔸 Agar hech kim topilmasa
    if ($students->isEmpty()) {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "❌ *Talaba topilmadi*\n\n"
                . "📝 Qidiruv so'rovi: `{$query}`\n\n"
                . "💡 Iltimos, boshqa ma'lumot bilan qayta urinib ko'ring.",
            'parse_mode' => 'Markdown'
        ]);
        return;
    }

    // 🔸 Topilgan talabalar sonini ko'rsatish
    $count = $students->count();
    
    if ($count > 20) {
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "⚠️ *Juda ko'p natija topildi!*\n\n"
                . "📊 Jami: *{$count} ta* talaba\n\n"
                . "💡 Iltimos, qidiruvni aniqroq kiriting.\n"
                . "_Masalan: to'liq ism, guruh nomeri yoki aniq fakultet nomi_",
            'parse_mode' => 'Markdown'
        ]);
        return;
    }

    Telegram::sendMessage([
        'chat_id' => $chatId,
        'text' => "✅ *Qidiruv natijalari*\n\n"
            . "📊 Topildi: *{$count} ta* talaba\n"
            . "🔎 So'rov: `{$query}`\n\n"
            . "⏳ Ma'lumotlar yuklanmoqda...",
        'parse_mode' => 'Markdown'
    ]);

    // 🔸 Har bir topilgan talaba uchun ma'lumotlarni yuborish
    foreach ($students as $index => $student) {
        sleep(1); // Telegram spam himoyasi uchun
        
        $text = "👤 *Student Ma'lumotlari* (".($index + 1)."/$count)\n\n";
        $text .= "🆔 *Talaba ID:* {$student->talaba_id}\n";
        $text .= "🧑‍🎓 *F.I.SH:* {$student->fish}\n";
        $text .= "🏛️ *Fakultet:* {$student->fakultet}\n";
        $text .= "👥 *Guruh:* {$student->guruh}\n";
        $text .= "📞 *Telefon:* {$student->telefon}\n";
        $text .= "👨‍🏫 *Tyutori:* {$student->tyutori}\n";
        $text .= "🌍 *Hudud:* {$student->hudud}\n\n";

        $text .= "📍 *Doimiy yashash manzili:*\n";
        $text .= "   • Viloyat: {$student->doimiy_yashash_viloyati}\n";
        $text .= "   • Tuman: {$student->doimiy_yashash_tumani}\n";
        $text .= "   • Manzil: {$student->doimiy_yashash_manzili}\n";
        if ($student->doimiy_yashash_manzili_urli) {
            $text .= "   • 🗺️ [Xaritada ko'rish]({$student->doimiy_yashash_manzili_urli})\n";
        }
        $text .= "\n";

        $text .= "🏘️ *Vaqtincha yashash manzili:*\n";
        $text .= "   • Viloyat: {$student->vaqtincha_yashash_viloyati}\n";
        $text .= "   • Tuman: {$student->vaqtincha_yashash_tumani}\n";
        $text .= "   • Manzil: {$student->vaqtincha_yashash_manzili}\n";
        if ($student->vaqtincha_yashash_manzili_urli) {
            $text .= "   • 🗺️ [Xaritada ko'rish]({$student->vaqtincha_yashash_manzili_urli})\n";
        }
        $text .= "\n";

        $text .= "🏠 *Uy egasi:* {$student->uy_egasi}\n";
        $text .= "📱 *Uy egasi telefoni:* {$student->uy_egasi_telefoni}\n";
        $text .= "🏨 *Yotoqxona nomeri:* {$student->yotoqxona_nomeri}\n";
        $text .= "💰 *Narxi:* {$student->narx}\n\n";

        $text .= "👨‍👩‍👧‍👦 *Ota-onasi:* {$student->ota_ona}\n";
        $text .= "📞 *Ota-onasi telefoni:* {$student->ota_ona_telefoni}\n";

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
            'disable_web_page_preview' => true,
            'reply_markup' => $reply_markup
        ]);
    }

    // 🔸 Qidiruv yakunlandi xabari
    Telegram::sendMessage([
        'chat_id' => $chatId,
        'text' => "✅ *Qidiruv yakunlandi*\n\n"
            . "📊 Jami ko'rsatildi: *{$count} ta* talaba\n\n"
            . "🔄 Yangi qidiruv uchun ma'lumot yuboring.",
        'parse_mode' => 'Markdown'
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
        $text .= "🧑‍🎓 *F.I.SH:* {$student->fish}\n";
        $text .= "🏛️ *Fakultet:* {$student->fakultet}\n";
        $text .= "👥 *Guruh:* {$student->guruh}\n";
        $text .= "📞 *Telefon:* {$student->telefon}\n";
        $text .= "👨‍🏫 *Tyutori:* {$student->tyutori}\n";
        $text .= "🌍 *Hudud:* {$student->hudud}\n\n";

        $text .= "📍 *Doimiy yashash manzili:*\n";
        $text .= "   • Viloyat: {$student->doimiy_yashash_viloyati}\n";
        $text .= "   • Tuman: {$student->doimiy_yashash_tumani}\n";
        $text .= "   • Manzil: {$student->doimiy_yashash_manzili}\n";
        $text .= "   🗺 [Xaritada ko'rish]({$student->doimiy_yashash_manzili_urli})\n\n";

        $text .= "🏘️ *Vaqtincha yashash manzili:*\n";

        if (isset($student->yotoqxona_nomeri)) {
            $text .= "🏨 *Yotoqxona nomeri:* {$student->yotoqxona_nomeri}\n";
        } else {
            $text .= "   • Viloyat: {$student->vaqtincha_yashash_viloyati}\n";
            $text .= "   • Tuman: {$student->vaqtincha_yashash_tumani}\n";
            $text .= "   • Manzil: {$student->vaqtincha_yashash_manzili}\n";
            $text .= "   🗺 [Xaritada ko'rish]({$student->vaqtincha_yashash_manzili_urli})\n\n";

            $text .= "🏠 *Uy egasi:* {$student->uy_egasi}\n";
            $text .= "📱 *Uy egasi telefoni:* {$student->uy_egasi_telefoni}\n";
        }
        $text .= "💰 *Narxi:* {$student->narx}\n\n";

        $text .= "👨‍👩‍👧‍👦 *Ota-onasi:* {$student->ota_ona}\n";
        $text .= "📞 *Ota-onasi telefoni:* {$student->ota_ona_telefoni}\n";


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
