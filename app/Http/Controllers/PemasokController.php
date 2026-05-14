<?php
namespace App\Http\Controllers;
use App\Models\Pemasok;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search','');
        $items  = Pemasok::when($search,fn($q)=>$q->where('nama','ilike',"%{$search}%"))->orderBy('nama')->get();
        return view('pemasok.index', compact('items','search'));
    }
    public function store(Request $request)
    {
        $d = $request->validate(['nama'=>'required|string|max:100','kontak'=>'nullable|string|max:100','no_hp'=>'nullable|string|max:20','alamat'=>'nullable|string','catatan'=>'nullable|string']);
        $d['user_id'] = auth()->id();
        Pemasok::create($d);
        return back()->with('success','Pemasok berhasil ditambahkan.');
    }
    public function update(Request $request, Pemasok $pemasok)
    {
        $pemasok->update($request->validate(['nama'=>'required|string|max:100','kontak'=>'nullable|string|max:100','no_hp'=>'nullable|string|max:20','alamat'=>'nullable|string','catatan'=>'nullable|string']));
        return back()->with('success','Pemasok berhasil diperbarui.');
    }
    public function destroy(Pemasok $pemasok)
    {
        $pemasok->delete();
        return back()->with('success','Pemasok berhasil dihapus.');
    }
}
