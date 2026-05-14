<?php
namespace App\Http\Controllers;
use App\Models\Inventori;
use App\Models\InventoriMovement;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoriController extends Controller
{
    public function index()
    {
        $items = Inventori::orderBy('nama_barang')->get();
        return view('inventori.index', compact('items'));
    }
    public function store(Request $request)
    {
        $d = $request->validate(['nama_barang'=>'required|string|max:200','jumlah_stok'=>'required|numeric|min:0','satuan'=>'required|string|max:20']);
        $d['user_id'] = auth()->id();
        $d['tanggal_update'] = now();
        Inventori::create($d);
        return back()->with('success','Barang berhasil ditambahkan.');
    }
    public function update(Request $request, Inventori $inventori)
    {
        $inventori->update($request->validate(['nama_barang'=>'required|string|max:200','satuan'=>'required|string|max:20']));
        return back()->with('success','Data inventori berhasil diperbarui.');
    }
    public function destroy(Inventori $inventori)
    {
        $inventori->delete();
        return back()->with('success','Barang berhasil dihapus.');
    }
    public function adjust(Request $request, Inventori $inventori, TelegramService $telegram)
    {
        $request->validate(['jenis'=>'required|in:masuk,keluar','jumlah'=>'required|numeric|min:0.01','keterangan'=>'nullable|string|max:255']);
        DB::transaction(function() use ($request, $inventori) {
            InventoriMovement::create(['inventori_id'=>$inventori->id,'jenis'=>$request->jenis,'jumlah'=>$request->jumlah,'keterangan'=>$request->keterangan,'tanggal'=>now(),'user_id'=>auth()->id()]);
            if ($request->jenis === 'masuk') $inventori->increment('jumlah_stok', $request->jumlah);
            else $inventori->decrement('jumlah_stok', $request->jumlah);
            $inventori->update(['tanggal_update'=>now()]);
        });
        $emoji = $request->jenis === 'masuk' ? '📥' : '📤';
        $telegram->broadcast("{$emoji} *UPDATE STOK*\nBarang: *{$inventori->nama_barang}*\nJenis: *".ucfirst($request->jenis)."*\nJumlah: *{$request->jumlah} {$inventori->satuan}*\nStok Baru: *{$inventori->fresh()->jumlah_stok} {$inventori->satuan}*");
        return back()->with('success','Stok berhasil disesuaikan.');
    }
}
