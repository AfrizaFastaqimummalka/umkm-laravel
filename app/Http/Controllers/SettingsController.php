<?php
namespace App\Http\Controllers;
use App\Models\TelegramSubscriber;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $subscribers = TelegramSubscriber::orderByDesc('created_at')->get();
        $rawToken    = config('telegram.bot_token');
        $botToken    = $rawToken ? '••••••••' . substr($rawToken, -6) : 'Belum diset';
        $allowedIds  = config('telegram.allowed_chat_ids', []);
        return view('settings.index', compact('subscribers','botToken','rawToken','allowedIds'));
    }
    public function toggleSubscriber(TelegramSubscriber $subscriber)
    {
        $subscriber->update(['is_active' => !$subscriber->is_active]);
        return back()->with('success','Status subscriber berhasil diubah.');
    }
}
