<?php
namespace App\Http\Controllers;
use App\Models\Pemasukan;
use Illuminate\Http\Request;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $items = Pemasukan::whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->orderByDesc('tanggal')->get();
        $total = $items->sum('jumlah');
        return view('pemasukan.index', compact('items','total','bulan','tahun'));
    }
    public function store(Request $request)
    {
        $d = $request->validate(['tanggal'=>'required|date','jumlah'=>'required|numeric|min:1','keterangan'=>'nullable|string|max:255','kategori'=>'required|in:penjualan,titip_jual,lainnya']);
        $d['user_id'] = auth()->id();
        Pemasukan::create($d);
        return back()->with('success','Pemasukan berhasil ditambahkan.');
    }
    public function update(Request $request, Pemasukan $pemasukan)
    {
        $pemasukan->update($request->validate(['tanggal'=>'required|date','jumlah'=>'required|numeric|min:1','keterangan'=>'nullable|string|max:255','kategori'=>'required|in:penjualan,titip_jual,lainnya']));
        return back()->with('success','Pemasukan berhasil diperbarui.');
    }
    public function destroy(Pemasukan $pemasukan)
    {
        $pemasukan->delete();
        return back()->with('success','Pemasukan berhasil dihapus.');
    }
}
