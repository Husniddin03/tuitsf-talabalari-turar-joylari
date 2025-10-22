<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class CallbackController extends Controller
{
    public function controll($update)
    {
        $data = $update->callbackQuery->data;
        $chatId = $update->callbackQuery->message->chat->id;
        $messageId = $update->callbackQuery->message->messageId;

        if (strpos($data, 'all_students_') === 0) {
            // Parse page number from callback data
            $parts = explode('_', $data);
            $page = isset($parts[2]) ? (int)$parts[2] : 1;
            $this->students($chatId, $messageId, $page);
        } elseif (strpos($data, 'start') === 0) {
            $mes = new MessagesController;
            $mes->start($update->callbackQuery->message);
        } elseif (strpos($data, 'search') === 0) {
            Telegram::sendMessage([
                'chat_id' => $update->callbackQuery->message->chat->id,
                'text' => "🔎 *Talaba qidiruv bo‘limi*\n\n"
                    . "Quyidagi ma’lumotlardan biri orqali talabani qidiring:\n\n"
                    . "• 👤 *F.I.Sh.* (Ism Familiya)\n"
                    . "• 🏫 *Fakultet*\n"
                    . "• 📞 *Telefon raqami*\n"
                    . "• 🧑‍🎓 *Guruh*\n"
                    . "• 👨‍🏫 *Tyutori*\n"
                    . "• 📍 *Hudud*\n\n"
                    . "_Masalan:_ `Kompyuter injiniringi` yoki `KI24-03`",
                'parse_mode' => 'Markdown',
            ]);
        }
    }

    public function students($chatId, $messageId, $page = 1)
    {
        $perPage = 10; // Har bir sahifada nechta student ko'rsatish

        // Studentlarni olish
        $students = Student::paginate($perPage, ['*'], 'page', $page);

        // Xabar matni
        $text = "📚 *Barcha Studentlar*\n\n";
        $text .= "Sahifa: {$page}/{$students->lastPage()}\n";
        $text .= "Jami: {$students->total()} ta student\n\n";

        if ($students->isEmpty()) {
            $text .= "Studentlar topilmadi.";
        } else {
            foreach ($students as $index => $student) {
                $number = ($page - 1) * $perPage + $index + 1;
                $text .= "{$number}. {$student->fish}\n";
                $text .= "      Tel: {$student->telefon}\n\n";
                $text .= "      👉 To‘liq ma’lumot: /student_{$student->id}\n\n";
            }
        }

        // Inline keyboard yaratish
        $keyboard = [];
        $buttons = [];

        // Previous button
        if ($students->currentPage() > 1) {
            $buttons[] = [
                'text' => '⬅️ Oldingi',
                'callback_data' => 'all_students_' . ($page - 1)
            ];
        }

        // Page info
        $buttons[] = [
            'text' => "📄 {$page}/{$students->lastPage()}",
            'callback_data' => 'page_info'
        ];

        // Next button
        if ($students->hasMorePages()) {
            $buttons[] = [
                'text' => 'Keyingi ➡️',
                'callback_data' => 'all_students_' . ($page + 1)
            ];
        }

        if (!empty($buttons)) {
            $keyboard[] = $buttons;
        }

        // Orqaga qaytish tugmasi
        $keyboard[] = [
            [
                'text' => '🔙 Orqaga',
                'callback_data' => 'start'
            ]
        ];

        $reply_markup = Keyboard::make([
            'inline_keyboard' => $keyboard
        ]);

        // Xabarni yangilash
        if (isset($update->callbackQuery->message->text)) {
            Telegram::editMessageText([
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => $reply_markup
            ]);
        } else {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => $reply_markup
            ]);
        }
    }
}
