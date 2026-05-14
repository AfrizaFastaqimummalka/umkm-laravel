@extends('layouts.app')
@section('title','Inventori')
@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Inventori</h1>
    <p class="page-sub">{{ $items->count() }} jenis barang terdaftar</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('m-add')">+ Tambah Barang</button>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>No</th><th>Nama Barang</th><th>Stok</th><th>Satuan</th>
        <th>Update Terakhir</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $i=>$item)
      <tr>
        <td class="tm" style="font-size:13px">{{ $i+1 }}</td>
        <td><div style="font-weight:600">{{ $item->nama_barang }}</div></td>
        <td>
          <span class="mono" style="font-size:16px;color:{{ $item->jumlah_stok <= 5 ? 'var(--red-600)' : 'var(--green-600)' }}">
            {{ number_format($item->jumlah_stok, 2, ',', '.') }}
          </span>
          @if($item->jumlah_stok <= 5)
            <span class="badge badge-red" style="margin-left:6px;font-size:10px">Stok Rendah</span>
          @endif
        </td>
        <td><span class="badge badge-gray">{{ $item->satuan_label }}</span></td>
        <td class="tm" style="font-size:13px">
          {{ $item->tanggal_update ? \Carbon\Carbon::parse($item->tanggal_update)->format('d M Y H:i') : '—' }}
        </td>
        <td>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            <button class="btn btn-secondary btn-sm"
              onclick="openAdjust({{ $item->id }},'{{ addslashes($item->nama_barang) }}','{{ $item->satuan }}')">
              📊 Sesuaikan
            </button>
            <button class="btn btn-secondary btn-sm"
              onclick="editInventori({{ $item->id }},'{{ addslashes($item->nama_barang) }}','{{ $item->satuan }}')">✏️</button>
            <form method="POST" action="{{ route('inventori.destroy',$item) }}"
              onsubmit="return confirm('Hapus barang {{ $item->nama_barang }}?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm" type="submit">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6"><div class="empty">
        <div class="empty-ico">📦</div>
        <p class="empty-ttl">Belum ada data inventori</p>
        <p class="empty-sub">Tambahkan bahan baku atau produk untuk mulai melacak stok</p>
        <button class="btn btn-primary btn-sm" onclick="openModal('m-add')">+ Tambah Sekarang</button>
      </div></td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="m-add">
  <div class="modal">
    <div class="modal-header"><h3 class="modal-title">Tambah Barang Inventori</h3><button class="modal-close" onclick="closeModal('m-add')">✕</button></div>
    <div class="modal-body">
      <form method="POST" action="{{ route('inventori.store') }}">
        @csrf
        <div class="form-group">
          <label class="form-label">Nama Barang *</label>
          <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Kedelai, Ragi Tempe" required>
        </div>
        <div class="form-group">
          <label class="form-label">Stok Awal *</label>
          <input type="number" name="jumlah_stok" class="form-control" placeholder="0" min="0" step="0.01" required>
        </div>
        <div class="form-group">
          <label class="form-label">Satuan *</label>
          <select name="satuan" class="form-control" required>
            <option value="kg">Kilogram (kg)</option>
            <option value="ons">Ons</option>
            <option value="pcs">Pieces (pcs)</option>
            <option value="pack">Pack</option>
            <option value="zak">Zak</option>
            <option value="liter">Liter</option>
            <option value="unit">Unit</option>
          </select>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
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
    <div class="modal-header"><h3 class="modal-title">Edit Barang</h3><button class="modal-close" onclick="closeModal('m-edit')">✕</button></div>
    <div class="modal-body">
      <form method="POST" id="form-edit-inv">
        @csrf @method('PUT')
        <div class="form-group">
          <label class="form-label">Nama Barang *</label>
          <input type="text" name="nama_barang" id="inv-nama" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Satuan *</label>
          <select name="satuan" id="inv-satuan" class="form-control" required>
            <option value="kg">Kilogram (kg)</option>
            <option value="ons">Ons</option>
            <option value="pcs">Pieces (pcs)</option>
            <option value="pack">Pack</option>
            <option value="zak">Zak</option>
            <option value="liter">Liter</option>
            <option value="unit">Unit</option>
          </select>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button type="button" class="btn btn-secondary" onclick="closeModal('m-edit')">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Adjust Stok --}}
<div class="modal-overlay" id="m-adjust">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Sesuaikan Stok</h3>
      <button class="modal-close" onclick="closeModal('m-adjust')">✕</button>
    </div>
    <div class="modal-body">
      <div style="margin-bottom:16px;padding:12px 14px;background:var(--green-50);border-radius:var(--rmd)">
        <p style="font-size:13px;color:var(--gray-600)">Barang:</p>
        <p style="font-weight:700;color:var(--gray-900)" id="adj-nama-display">—</p>
      </div>
      <form method="POST" id="form-adjust">
        @csrf
        <div class="form-group">
          <label class="form-label">Jenis Penyesuaian *</label>
          <div style="display:flex;gap:10px">
            <label style="display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid var(--gray-300);border-radius:var(--rsm);cursor:pointer;flex:1;transition:all .15s" id="lbl-masuk">
              <input type="radio" name="jenis" value="masuk" required onchange="toggleJenis('masuk')"> 📥 Masuk
            </label>
            <label style="display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid var(--gray-300);border-radius:var(--rsm);cursor:pointer;flex:1;transition:all .15s" id="lbl-keluar">
              <input type="radio" name="jenis" value="keluar" onchange="toggleJenis('keluar')"> 📤 Keluar
            </label>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah *</label>
          <div style="position:relative">
            <input type="number" name="jumlah" id="adj-jumlah" class="form-control" placeholder="0" min="0.01" step="0.01" required style="padding-right:60px">
            <span id="adj-satuan" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--gray-400);font-size:13px"></span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Beli dari supplier, Produksi batch #12">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button type="button" class="btn btn-secondary" onclick="closeModal('m-adjust')">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function editInventori(id,nama,satuan){
  document.getElementById('form-edit-inv').action='/inventori/'+id;
  document.getElementById('inv-nama').value=nama;
  document.getElementById('inv-satuan').value=satuan;
  openModal('m-edit');
}
function openAdjust(id,nama,satuan){
  document.getElementById('form-adjust').action='/inventori/'+id+'/adjust';
  document.getElementById('adj-nama-display').textContent=nama;
  document.getElementById('adj-satuan').textContent=satuan;
  document.getElementById('adj-jumlah').value='';
  document.querySelectorAll('input[name="jenis"]').forEach(r=>r.checked=false);
  openModal('m-adjust');
}
function toggleJenis(val){
  document.getElementById('lbl-masuk').style.borderColor=val==='masuk'?'var(--green-500)':'var(--gray-300)';
  document.getElementById('lbl-masuk').style.background=val==='masuk'?'var(--green-50)':'#fff';
  document.getElementById('lbl-keluar').style.borderColor=val==='keluar'?'var(--red-500)':'var(--gray-300)';
  document.getElementById('lbl-keluar').style.background=val==='keluar'?'var(--red-50)':'#fff';
}
</script>
@endpush
