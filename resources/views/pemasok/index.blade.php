@extends('layouts.app')
@section('title','Pemasok')
@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Pemasok</h1>
    <p class="page-sub">{{ $items->count() }} pemasok terdaftar</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('m-add')">+ Tambah Pemasok</button>
</div>

<form method="GET" style="margin-bottom:20px;max-width:340px;position:relative">
  <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--gray-400)">🔍</span>
  <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pemasok..." class="form-control" style="padding-left:36px">
</form>

<div class="table-wrap">
  <table>
    <thead><tr><th>No</th><th>Nama</th><th>Kontak</th><th>No. HP</th><th>Alamat</th><th>Catatan</th><th>Aksi</th></tr></thead>
    <tbody>
      @forelse($items as $i=>$item)
      <tr>
        <td class="tm" style="font-size:13px">{{ $i+1 }}</td>
        <td><div style="font-weight:600">{{ $item->nama }}</div></td>
        <td class="tm">{{ $item->kontak ?: '—' }}</td>
        <td class="tm">{{ $item->no_hp ?: '—' }}</td>
        <td class="tm" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $item->alamat ?: '—' }}</td>
        <td class="tm">{{ $item->catatan ? \Str::limit($item->catatan,30) : '—' }}</td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn btn-secondary btn-sm" onclick="editPemasok({{ $item->id }},'{{ addslashes($item->nama) }}','{{ addslashes($item->kontak) }}','{{ $item->no_hp }}','{{ addslashes($item->alamat) }}','{{ addslashes($item->catatan) }}')">✏️</button>
            <form method="POST" action="{{ route('pemasok.destroy',$item) }}" onsubmit="return confirm('Hapus pemasok {{ $item->nama }}?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm" type="submit">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="7"><div class="empty">
        <div class="empty-ico">🚛</div>
        <p class="empty-ttl">{{ $search ? "Tidak ada hasil untuk \"$search\"" : 'Belum ada pemasok terdaftar' }}</p>
        @if(!$search)<button class="btn btn-primary btn-sm" onclick="openModal('m-add')">+ Tambah Sekarang</button>@endif
      </div></td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="modal-overlay" id="m-add">
  <div class="modal">
    <div class="modal-header"><h3 class="modal-title">Tambah Pemasok</h3><button class="modal-close" onclick="closeModal('m-add')">✕</button></div>
    <div class="modal-body">
      <form method="POST" action="{{ route('pemasok.store') }}">
        @csrf
        <div class="form-group"><label class="form-label">Nama *</label><input type="text" name="nama" class="form-control" placeholder="Nama pemasok" required></div>
        <div class="form-group"><label class="form-label">Kontak / PIC</label><input type="text" name="kontak" class="form-control" placeholder="Nama kontak person"></div>
        <div class="form-group"><label class="form-label">No. HP</label><input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789"></div>
        <div class="form-group"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea></div>
        <div class="form-group"><label class="form-label">Catatan</label><textarea name="catatan" class="form-control" rows="2" placeholder="Opsional"></textarea></div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button type="button" class="btn btn-secondary" onclick="closeModal('m-add')">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal-overlay" id="m-edit">
  <div class="modal">
    <div class="modal-header"><h3 class="modal-title">Edit Pemasok</h3><button class="modal-close" onclick="closeModal('m-edit')">✕</button></div>
    <div class="modal-body">
      <form method="POST" id="form-edit-ps">
        @csrf @method('PUT')
        <div class="form-group"><label class="form-label">Nama *</label><input type="text" name="nama" id="ps-nama" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Kontak / PIC</label><input type="text" name="kontak" id="ps-kontak" class="form-control"></div>
        <div class="form-group"><label class="form-label">No. HP</label><input type="text" name="no_hp" id="ps-nohp" class="form-control"></div>
        <div class="form-group"><label class="form-label">Alamat</label><textarea name="alamat" id="ps-alamat" class="form-control" rows="2"></textarea></div>
        <div class="form-group"><label class="form-label">Catatan</label><textarea name="catatan" id="ps-catatan" class="form-control" rows="2"></textarea></div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button type="button" class="btn btn-secondary" onclick="closeModal('m-edit')">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
function editPemasok(id,nama,kontak,nohp,alamat,catatan){
  document.getElementById('form-edit-ps').action='/pemasok/'+id;
  document.getElementById('ps-nama').value=nama;
  document.getElementById('ps-kontak').value=kontak;
  document.getElementById('ps-nohp').value=nohp;
  document.getElementById('ps-alamat').value=alamat;
  document.getElementById('ps-catatan').value=catatan;
  openModal('m-edit');
}
</script>
@endpush
