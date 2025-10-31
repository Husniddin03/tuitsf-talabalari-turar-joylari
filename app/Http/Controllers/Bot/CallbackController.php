<?php

namespace App\Http\Controllers\Bot;

use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Telegram\Bot\FileUpload\InputFile;
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
        } elseif ($data == 'start') {
            $mes = new MessagesController;
            $mes->start($update->callbackQuery->message);
        } elseif (strpos($data, 'search') === 0) {
            Telegram::sendMessage([
                'chat_id' => $update->callbackQuery->message->chat->id,
                'text' => "🔎 *Talaba qidiruv bo'limi*\n\n"
                    . "Quyidagi ma'lumotlardan biri orqali talabani qidiring:\n\n"
                    . "• 👤 *F.I.Sh.* (Ism Familiya)\n"
                    . "• 🆔 *ID* (Talaba ID)\n"
                    . "• 🏛️ *Fakultet*\n"
                    . "• 📞 *Telefon raqami*\n"
                    . "• 👥 *Guruh*\n"
                    . "• 👨‍🏫 *Tyutori*\n"
                    . "• 🌍 *Hudud*\n"
                    . "• 🏨 *Yotoqxona nomeri*\n"
                    . "• 👨‍👩‍👧‍👦 *Ota-ona*\n\n"
                    . "_Masalan:_ `Kompyuter injiniringi` yoki `KI24-03` yoki `Andijon`",
                'parse_mode' => 'Markdown',
            ]);
        } elseif (strpos($data, 'download') === 0) {
            $this->download($update->callbackQuery->message);
        } elseif (preg_match('/^(fakultet|guruh|hudud|tyutor)/', $data)) {
            $this->group($update->callbackQuery->message, $data);
        } elseif (strpos($data, 'findcolumn_') === 0) {
            $payload = str_replace('findcolumn_', '', $data);
            $pos = strrpos($payload, '_');
            $name = substr($payload, 0, $pos);
            $column = substr($payload, $pos + 1);

            $this->finishDownload($update->callbackQuery->message, $column, $name);
        } elseif ($data == 'all_excel') {
            $this->finishDownload($update->callbackQuery->message, 'all', 'all');
        }
    }


    public function finishDownload($message, $column, $name)
    {
        if ($column == 'all' && $name == 'all') {
            $data = Student::all();
        } else {
            $data = Student::where($column, 'LIKE', "%{$name}%")->get();
        }

        if ($data->isEmpty()) {
            Telegram::sendMessage([
                'chat_id' => $message->chat->id,
                'text' => "Ma'lumot topilmadi."
            ]);
            return;
        }

        $export = new StudentExport($data);

        $fileName = 'students_' . time() . '.xlsx';

        // Public papkaga saqlash
        Excel::store($export, $fileName, 'public');

        $filePath = storage_path('app/public/' . $fileName);

        // Fayl mavjudligini tekshirish
        if (!file_exists($filePath)) {
            Telegram::sendMessage([
                'chat_id' => $message->chat->id,
                'text' => "Fayl yaratishda xatolik yuz berdi."
            ]);
            return;
        }

        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => "Barcha talabalar", 'callback_data' => "all_students_1"]),
                Keyboard::inlineButton(['text' => "Qidirish", 'callback_data' => "search"]),
                Keyboard::inlineButton(['text' => "Excel yuklash", 'callback_data' => "download"])
            ]);

        // Telegram ga yuborish
        Telegram::sendDocument([
            'chat_id' => $message->chat->id,
            'document' => InputFile::create($filePath, $fileName),
            'caption' => "📊 Talabalar ro'yxati\n\n$column: $name\nJami: " . $data->count() . " ta",
            'reply_markup' => $keyboard
        ]);

        // Faylni o'chirish
        unlink($filePath);
    }

    public function group($message, $data)
    {
        // To'g'ri yozilishi
        $columns = Student::distinct()->pluck($data);

        $keyboard = Keyboard::make()->inline();

        foreach ($columns as $columnValue) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => $columnValue,
                    'callback_data' => "findcolumn_" . substr($columnValue, 0, 5) . "_" . $data
                ])
            ]);
        }

        $keyboard->row([
            Keyboard::inlineButton(['text' => "Orqaga", 'callback_data' => "download"])
        ]);

        Telegram::sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'Kerakli ' . $data . ' ni tanlang!', // Dinamik xabar
            'reply_markup' => $keyboard,
        ]);
    }



    public function download($message)
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => "Fakultet", 'callback_data' => "fakultet"]),
                Keyboard::inlineButton(['text' => "Guruh", 'callback_data' => "guruh"])
            ])->row([
                Keyboard::inlineButton(['text' => "Hudut", 'callback_data' => "hudud"]),
                Keyboard::inlineButton(['text' => "Tyutor", 'callback_data' => "tyutori"])
            ])->row([
                Keyboard::inlineButton(['text' => "Barcha talabalar", 'callback_data' => "all_excel"]),
            ])->row([
                Keyboard::inlineButton(['text' => "Orqaga", 'callback_data' => "start"]),
            ]);
        Telegram::sendPhoto([
            'chat_id' => $message->chat->id,
            'photo' => InputFile::create(public_path('images/excel.png')),
            'caption' => "Kerakli Bo'limni tanlang!",
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
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
                $text .= "      ID: {$student->talaba_id}\n";
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
