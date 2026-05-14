<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\TelegramSubscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    private string $api;

    public function __construct()
    {
        $token     = config('telegram.bot_token');
        $this->api = "https://api.telegram.org/bot{$token}";
    }

    public function send(int|string $chatId, string $text): void
    {
        Http::post("{$this->api}/sendMessage", ['chat_id'=>$chatId,'text'=>$text,'parse_mode'=>'Markdown']);
    }

    public function sendDoc(int|string $chatId, string $path, string $caption): void
    {
        Http::attach('document', file_get_contents($path), basename($path))
            ->post("{$this->api}/sendDocument", ['chat_id'=>$chatId,'caption'=>$caption,'parse_mode'=>'Markdown']);
    }

    public function broadcast(string $text): void
    {
        $subscribers = TelegramSubscriber::where('is_active', true)->get();
        foreach ($subscribers as $subscriber) {
            $this->send($subscriber->chat_id, $text);
        }
    }

    public function isAllowed(int|string $chatId): bool
    {
        // First check config for hardcoded admins
        $allowed = config('telegram.allowed_chat_ids', []);
        if (in_array((string)$chatId, array_map('strval', $allowed))) {
            return true;
        }

        // Then check database for active subscribers
        return TelegramSubscriber::where('chat_id', $chatId)->where('is_active', true)->exists();
    }

    public function handleUpdate(array $update): void
    {
        $message = $update['message'] ?? null;
        if (!$message) return;

        $chatId = $message['chat']['id'];
        $text   = $message['text'] ?? '';
        $from   = $message['from'] ?? [];

        // Save or update subscriber info on every interaction
        TelegramSubscriber::updateOrCreate(
            ['chat_id' => $chatId],
            [
                'username'   => $from['username'] ?? null,
                'first_name' => $from['first_name'] ?? null,
                'last_name'  => $from['last_name'] ?? null,
            ]
        );

        if (!$this->isAllowed($chatId)) {
            $this->send($chatId, '⛔ Akses ditolak. Bot ini hanya untuk admin UMKM Tempe.');
            return;
        }

        if (!str_starts_with($text, '/')) {
            $this->send($chatId, 'Gunakan perintah /rekap atau /help');
            return;
        }

        $parts   = explode(' ', trim($text));
        $command = strtolower(explode('@', $parts[0])[0]);
        $args    = array_slice($parts, 1);

        match ($command) {
            '/start', '/help' => $this->cmdStart($chatId),
            '/rekap'          => $this->cmdRekap($chatId, $args),
            default           => $this->send($chatId, "Perintah `{$command}` tidak dikenal. Ketik /help"),
        };
    }

    private function cmdStart(int|string $chatId): void
    {
        $this->send($chatId,
            "👋 *Halo! Bot UMKM Tempe aktif.*\n\n"
            . "📊 `/rekap` — Rekap bulan ini\n"
            . "📊 `/rekap harian` — Rekap hari ini\n"
            . "📊 `/rekap bulanan` — Rekap bulan ini\n"
            . "📊 `/rekap tahunan` — Rekap tahun ini"
        );
    }

    private function cmdRekap(int|string $chatId, array $args): void
    {
        $tipe = strtolower($args[0] ?? 'bulanan');
        $now  = Carbon::now('Asia/Jakarta');

        $this->send($chatId, '⏳ Sedang membuat laporan...');

        try {
            [$dari, $sampai, $label] = match ($tipe) {
                'harian','hari'   => [$now->copy()->startOfDay(),  $now->copy()->endOfDay(),  $now->translatedFormat('d F Y')],
                'tahunan','tahun' => [$now->copy()->startOfYear(), $now->copy()->endOfYear(), 'Tahun '.$now->year],
                default           => [$now->copy()->startOfMonth(),$now->copy()->endOfMonth(),$now->translatedFormat('F Y')],
            };

            $masuk  = Pemasukan::whereBetween('tanggal',[$dari,$sampai])->sum('jumlah');
            $keluar = Pengeluaran::whereBetween('tanggal',[$dari,$sampai])->sum('jumlah');
            $saldo  = $masuk - $keluar;
            $icon   = $saldo >= 0 ? '✅' : '⚠️';

            $caption =
                "📊 *REKAP KEUANGAN UMKM TEMPE*\n"
                ."📅 Periode: *{$label}*\n"
                .str_repeat('─',28)."\n\n"
                ."💰 *Total Pemasukan*\n"
                .'   `Rp '.number_format($masuk,0,',','.')."`\n\n"
                ."💸 *Total Pengeluaran*\n"
                .'   `Rp '.number_format($keluar,0,',','.')."`\n\n"
                .str_repeat('─',28)."\n"
                ."{$icon} *Saldo Bersih:* `Rp ".number_format($saldo,0,',','.').'`';

            // Generate Excel
            $pemasukan   = Pemasukan::whereBetween('tanggal',[$dari,$sampai])->orderBy('tanggal')->get();
            $pengeluaran = Pengeluaran::whereBetween('tanggal',[$dari,$sampai])->orderBy('tanggal')->get();

            $data = [
                'label'             => $label,
                'total_pemasukan'   => $masuk,
                'total_pengeluaran' => $keluar,
                'saldo'             => $saldo,
                'pemasukan'         => $pemasukan->map(fn($i)=>['tanggal'=>$i->tanggal->format('Y-m-d'),'jumlah'=>$i->jumlah,'keterangan'=>$i->keterangan,'kategori'=>$i->kategori])->toArray(),
                'pengeluaran'       => $pengeluaran->map(fn($i)=>['tanggal'=>$i->tanggal->format('Y-m-d'),'jumlah'=>$i->jumlah,'keterangan'=>$i->keterangan,'kategori'=>$i->kategori])->toArray(),
            ];

            $file = app(ExcelService::class)->generate($data);
            $this->sendDoc($chatId, $file, $caption);
            @unlink($file);

        } catch (\Throwable $e) {
            $this->send($chatId, "❌ Gagal membuat laporan.\nError: `{$e->getMessage()}`");
        }
    }
}
