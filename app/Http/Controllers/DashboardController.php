<?php
namespace App\Http\Controllers;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'bulan');
        $now     = Carbon::now('Asia/Jakarta');

        [$dari, $sampai, $label] = match ($periode) {
            'hari'  => [$now->copy()->startOfDay(),   $now->copy()->endOfDay(),   $now->translatedFormat('d F Y')],
            'tahun' => [$now->copy()->startOfYear(),  $now->copy()->endOfYear(),  'Tahun '.$now->year],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), $now->translatedFormat('F Y')],
        };

        $totalMasuk  = (float) Pemasukan::whereBetween('tanggal',[$dari,$sampai])->sum('jumlah');
        $totalKeluar = (float) Pengeluaran::whereBetween('tanggal',[$dari,$sampai])->sum('jumlah');
        $saldo       = $totalMasuk - $totalKeluar;

        $chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $chart[] = [
                'tanggal'     => $day->format('d/m'),
                'pemasukan'   => (float) Pemasukan::whereDate('tanggal',$day->toDateString())->sum('jumlah'),
                'pengeluaran' => (float) Pengeluaran::whereDate('tanggal',$day->toDateString())->sum('jumlah'),
            ];
        }

        return view('dashboard.index', compact('totalMasuk','totalKeluar','saldo','label','periode','chart'));
    }
}
