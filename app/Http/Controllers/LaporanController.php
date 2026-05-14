<?php
namespace App\Http\Controllers;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Services\ExcelService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index() { return view('laporan.index'); }

    public function generate(Request $request)
    {
        $tipe = $request->get('tipe','bulanan');
        $now  = Carbon::now('Asia/Jakarta');
        [$dari,$sampai,$label] = $this->range($tipe,$request,$now);
        $pemasukan   = Pemasukan::whereBetween('tanggal',[$dari,$sampai])->orderBy('tanggal')->get();
        $pengeluaran = Pengeluaran::whereBetween('tanggal',[$dari,$sampai])->orderBy('tanggal')->get();
        $totalMasuk  = $pemasukan->sum('jumlah');
        $totalKeluar = $pengeluaran->sum('jumlah');
        $saldo       = $totalMasuk - $totalKeluar;
        return view('laporan.index',compact('pemasukan','pengeluaran','totalMasuk','totalKeluar','saldo','label','tipe','dari','sampai'));
    }

    public function export(Request $request, ExcelService $excel)
    {
        $tipe = $request->get('tipe','bulanan');
        $now  = Carbon::now('Asia/Jakarta');
        [$dari,$sampai,$label] = $this->range($tipe,$request,$now);
        $pemasukan   = Pemasukan::whereBetween('tanggal',[$dari,$sampai])->orderBy('tanggal')->get();
        $pengeluaran = Pengeluaran::whereBetween('tanggal',[$dari,$sampai])->orderBy('tanggal')->get();
        $data = [
            'label'             => $label,
            'total_pemasukan'   => $pemasukan->sum('jumlah'),
            'total_pengeluaran' => $pengeluaran->sum('jumlah'),
            'saldo'             => $pemasukan->sum('jumlah') - $pengeluaran->sum('jumlah'),
            'pemasukan'         => $pemasukan->map(fn($i)=>['tanggal'=>$i->tanggal->format('Y-m-d'),'jumlah'=>$i->jumlah,'keterangan'=>$i->keterangan,'kategori'=>$i->kategori])->toArray(),
            'pengeluaran'       => $pengeluaran->map(fn($i)=>['tanggal'=>$i->tanggal->format('Y-m-d'),'jumlah'=>$i->jumlah,'keterangan'=>$i->keterangan,'kategori'=>$i->kategori])->toArray(),
        ];
        $file = $excel->generate($data);
        return response()->download($file,"laporan_{$tipe}_{$now->format('Ymd')}.xlsx",['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->deleteFileAfterSend();
    }

    private function range(string $tipe, Request $req, Carbon $now): array
    {
        return match($tipe) {
            'harian'  => [Carbon::parse($req->get('tanggal',$now->toDateString()))->startOfDay(),Carbon::parse($req->get('tanggal',$now->toDateString()))->endOfDay(),Carbon::parse($req->get('tanggal',$now->toDateString()))->translatedFormat('d F Y')],
            'tahunan' => [Carbon::create($req->get('tahun',$now->year))->startOfYear(),Carbon::create($req->get('tahun',$now->year))->endOfYear(),'Tahun '.$req->get('tahun',$now->year)],
            default   => [Carbon::create($req->get('tahun',$now->year),$req->get('bulan',$now->month))->startOfMonth(),Carbon::create($req->get('tahun',$now->year),$req->get('bulan',$now->month))->endOfMonth(),Carbon::create($req->get('tahun',$now->year),$req->get('bulan',$now->month))->translatedFormat('F Y')],
        };
    }
}
