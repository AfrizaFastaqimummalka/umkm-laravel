@extends('layouts.app')
@section('title','Pengeluaran')
@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Pengeluaran</h1>
    <p class="page-sub">{{ $items->count() }} transaksi — Total: <strong class="tr">Rp {{ number_format($total,0,',','.') }}</strong></p>
  </div>
  <button class="btn btn-primary" onclick="openModal('m-add')">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Tambah Pengeluaran
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
          @php $bm=['bahan_baku'=>'amber','operasional'=>'gray','gaji'=>'green','listrik_air'=>'gray','lainnya'=>'gray']; @endphp
          <span class="badge badge-{{ $bm[$item->kategori]??'gray' }}">{{ $item->kategori_label }}</span>
        </td>
        <td class="tm">{{ $item->keterangan ?: '—' }}</td>
        <td style="text-align:right"><span class="mono tr">Rp {{ number_format($item->jumlah,0,',','.') }}</span></td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn btn-secondary btn-sm" onclick="editPengeluaran({{ $item->id }},'{{ $item->tanggal->format('Y-m-d') }}','{{ $item->jumlah }}','{{ addslashes($item->keterangan) }}','{{ $item->kategori }}')">✏️</button>
            <form method="POST" action="{{ route('pengeluaran.destroy',$item) }}" onsubmit="return confirm('Hapus pengeluaran ini?')">
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
          <p class="empty-ttl">Belum ada data pengeluaran</p>
          <p class="empty-sub">Klik tombol Tambah Pengeluaran untuk mulai mencatat</p>
          <button class="btn btn-primary btn-sm" onclick="openModal('m-add')">+ Tambah Sekarang</button>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="modal-overlay" id="m-add">
  <div class="modal">
    <div class="modal-header"><h3 class="modal-title">Tambah Pengeluaran</h3><button class="modal-close" onclick="closeModal('m-add')">✕</button></div>
    <div class="modal-body">
      <form method="POST" action="{{ route('pengeluaran.store') }}">
        @csrf
        <div class="form-group"><label class="form-label">Tanggal *</label><input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required></div>
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="kategori" class="form-control" required>
            <option value="bahan_baku">Bahan Baku</option>
            <option value="operasional">Operasional</option>
            <option value="gaji">Gaji Karyawan</option>
            <option value="listrik_air">Listrik & Air</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Jumlah (Rp) *</label><input type="number" name="jumlah" class="form-control" placeholder="Contoh: 75000" min="1" required></div>
        <div class="form-group"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="Catatan singkat (opsional)"></div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
          <button type="button" class="btn btn-secondary" onclick="closeModal('m-add')">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal-overlay" id="m-edit">
  <div class="modal">
    <div class="modal-header"><h3 class="modal-title">Edit Pengeluaran</h3><button class="modal-close" onclick="closeModal('m-edit')">✕</button></div>
    <div class="modal-body">
      <form method="POST" id="form-edit-k">
        @csrf @method('PUT')
        <div class="form-group"><label class="form-label">Tanggal *</label><input type="date" name="tanggal" id="ek-tanggal" class="form-control" required></div>
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="kategori" id="ek-kategori" class="form-control" required>
            <option value="bahan_baku">Bahan Baku</option>
            <option value="operasional">Operasional</option>
            <option value="gaji">Gaji Karyawan</option>
            <option value="listrik_air">Listrik & Air</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Jumlah (Rp) *</label><input type="number" name="jumlah" id="ek-jumlah" class="form-control" min="1" required></div>
        <div class="form-group"><label class="form-label">Keterangan</label><input type="text" name="keterangan" id="ek-keterangan" class="form-control"></div>
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
function editPengeluaran(id,tanggal,jumlah,ket,kat){
  document.getElementById('form-edit-k').action='/pengeluaran/'+id;
  document.getElementById('ek-tanggal').value=tanggal;
  document.getElementById('ek-jumlah').value=jumlah;
  document.getElementById('ek-keterangan').value=ket;
  document.getElementById('ek-kategori').value=kat;
  openModal('m-edit');
}
</script>
@endpush
