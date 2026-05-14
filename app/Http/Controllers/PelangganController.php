<?php
namespace App\Http\Controllers;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search','');
        $items  = Pelanggan::when($search,fn($q)=>$q->where('nama','ilike',"%{$search}%"))->orderBy('nama')->get();
        return view('pelanggan.index', compact('items','search'));
    }
    public function store(Request $request)
    {
        $d = $request->validate(['nama'=>'required|string|max:100','no_hp'=>'nullable|string|max:20','alamat'=>'nullable|string','catatan'=>'nullable|string']);
        $d['user_id'] = auth()->id();
        Pelanggan::create($d);
        return back()->with('success','Pelanggan berhasil ditambahkan.');
    }
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $pelanggan->update($request->validate(['nama'=>'required|string|max:100','no_hp'=>'nullable|string|max:20','alamat'=>'nullable|string','catatan'=>'nullable|string']));
        return back()->with('success','Pelanggan berhasil diperbarui.');
    }
    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        return back()->with('success','Pelanggan berhasil dihapus.');
    }
}
