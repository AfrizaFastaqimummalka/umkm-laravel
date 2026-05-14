<?php
namespace App\Http\Controllers;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $items = Pengeluaran::whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->orderByDesc('tanggal')->get();
        $total = $items->sum('jumlah');
        return view('pengeluaran.index', compact('items','total','bulan','tahun'));
    }
    public function store(Request $request)
    {
        $d = $request->validate(['tanggal'=>'required|date','jumlah'=>'required|numeric|min:1','keterangan'=>'nullable|string|max:255','kategori'=>'required|in:bahan_baku,operasional,gaji,listrik_air,lainnya']);
        $d['user_id'] = auth()->id();
        Pengeluaran::create($d);
        return back()->with('success','Pengeluaran berhasil ditambahkan.');
    }
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $pengeluaran->update($request->validate(['tanggal'=>'required|date','jumlah'=>'required|numeric|min:1','keterangan'=>'nullable|string|max:255','kategori'=>'required|in:bahan_baku,operasional,gaji,listrik_air,lainnya']));
        return back()->with('success','Pengeluaran berhasil diperbarui.');
    }
    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();
        return back()->with('success','Pengeluaran berhasil dihapus.');
    }
}
