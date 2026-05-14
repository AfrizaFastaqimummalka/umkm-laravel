@extends('layouts.app')
@section('title','Pemasukan')
@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Pemasukan</h1>
    <p class="page-sub">{{ $items->count() }} transaksi — Total: <strong class="tg">Rp {{ number_format($total,0,',','.') }}</strong></p>
  </div>
  <button class="btn btn-primary" onclick="openModal('m-add')">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Tambah Pemasukan
  </button>
</div>

<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
    <label>Bulan:</label>
    <select name="bulan" class="fsel" onchange="this.form.submit()">
      @foreach(['Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'Mei'=>5,'Jun'=>6,'Jul'=>7,'Ags'=>8,'Sep'=>9,'Okt'=>10,'Nov'=>11,'Des'=>12] as $n=>$v)
        <option value="{{ $v }}" {{ $bulan==$v?'selected':'' }}>{{ $n }}</option>
      @endforeach
    </select>
    <label>Tahun:</label>
    <select name="tahun" class="fsel" onchange="this.form.submit()">
      @foreach([2023,2024,2025,2026] as $y)
        <option value="{{ $y }}" {{ $tahun==$y?'selected':'' }}>{{ $y }}</option>
      @endforeach
    </select>
  </form>
</div>

<div class="table-wrap">
  <table>
    <thead><tr><th>No</th><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th style="text-align:right">Jumlah</th><th>Aksi</th></tr></thead>
    <tbody>
      @forelse($items as $i=>$item)
      <tr>
        <td class="tm" style="font-size:13px">{{ $i+1 }}</td>
        <td style="white-space:nowrap">{{ $item->tanggal->format('d M Y') }}</td>
        <td>
          @php $bm=['penjualan'=>'green','titip_jual'=>'amber','lainnya'=>'gray']; @endphp
          <span class="badge badge-{{ $bm[$item->kategori]??'gray' }}">{{ $item->kategori_label }}</span>
        </td>
        <td class="tm">{{ $item->keterangan ?: '—' }}</td>
        <td style="text-align:right"><span class="mono tg">Rp {{ number_format($item->jumlah,0,',','.') }}</span></td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn btn-secondary btn-sm" onclick="editPemasukan({{ $item->id }},'{{ $item->tanggal->format('Y-m-d') }}','{{ $item->jumlah }}','{{ addslashes($item->keterangan) }}','{{ $item->kategori }}')">✏️</button>
            <form method="POST" action="{{ route('pemasukan.destroy',$item) }}" onsubmit="return confirm('Hapus pemasukan ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm" type="submit">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6">
        <div class="empty">
          <div class="empty-ico">📋</div>
          <p class="empty-ttl">Belum ada data pemasukan</p>
          <p class="empty-sub">Klik tombol Tambah Pemasukan untuk mulai mencatat</p>
          <button class="btn btn-primary btn-sm" onclick="openModal('m-add')">+ Tambah Sekarang</button>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="m-add">
  <div class="modal">
    <div class="modal-header"><h3 class="modal-title">Tambah Pemasukan</h3><button class="modal-close" onclick="closeModal('m-add')">✕</button></div>
    <div class="modal-body">
      <form method="POST" action="{{ route('pemasukan.store') }}">
        @csrf
        <div class="form-group">
          <label class="form-label">Tanggal *</label>
          <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="kategori" class="form-control" required>
            <option value="penjualan">Penjualan Tempe</option>
            <option value="titip_jual">Titip Jual</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah (Rp) *</label>
          <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 150000" min="1" required>
        </div>
        <div class="form-group">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" class="form-control" placeholder="Catatan singkat (opsional)">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
          <button type="button" class="btn btn-secondary" onclick="closeModal('m-add')">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Edit --}}
<div class="modal-overlay" id="m-edit">
  <div class="modal">
    <div class="modal-header"><h3 class="modal-title">Edit Pemasukan</h3><button class="modal-close" onclick="closeModal('m-edit')">✕</button></div>
    <div class="modal-body">
      <form method="POST" id="form-edit-p">
        @csrf @method('PUT')
        <div class="form-group">
          <label class="form-label">Tanggal *</label>
          <input type="date" name="tanggal" id="ep-tanggal" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="kategori" id="ep-kategori" class="form-control" required>
            <option value="penjualan">Penjualan Tempe</option>
            <option value="titip_jual">Titip Jual</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah (Rp) *</label>
          <input type="number" name="jumlah" id="ep-jumlah" class="form-control" min="1" required>
        </div>
        <div class="form-group">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" id="ep-keterangan" class="form-control">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
          <button type="button" class="btn btn-secondary" onclick="closeModal('m-edit')">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
function editPemasukan(id,tanggal,jumlah,ket,kat){
  document.getElementById('form-edit-p').action='/pemasukan/'+id;
  document.getElementById('ep-tanggal').value=tanggal;
  document.getElementById('ep-jumlah').value=jumlah;
  document.getElementById('ep-keterangan').value=ket;
  document.getElementById('ep-kategori').value=kat;
  openModal('m-edit');
}
</script>
@endpush
