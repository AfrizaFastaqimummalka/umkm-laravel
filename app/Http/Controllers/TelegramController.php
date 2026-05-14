<?php
namespace App\Http\Controllers;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function webhook(Request $request, TelegramService $telegram): JsonResponse
    {
        $update = $request->json()->all();
        if (!empty($update)) $telegram->handleUpdate($update);
        return response()->json(['ok' => true]);
    }
}
