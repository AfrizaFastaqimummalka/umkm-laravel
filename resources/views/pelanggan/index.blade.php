@extends('layouts.app')
@section('title','Pelanggan')
@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Pelanggan</h1>
    <p class="page-sub">{{ $items->count() }} pelanggan terdaftar</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('m-add')">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Tambah Pelanggan
  </button>
</div>

<form method="GET" style="margin-bottom:20px;max-width:340px;position:relative">
  <div style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--gray-400);pointer-events:none">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
  </div>
  <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pelanggan..."
    class="form-control" style="padding-left:36px">
</form>

<div class="table-wrap">
  <table>
    <thead><tr><th>No</th><th>Nama</th><th>No. HP</th><th>Alamat</th><th>Catatan</th><th>Aksi</th></tr></thead>
    <tbody>
      @forelse($items as $i=>$item)
      <tr>
        <td class="tm" style="font-size:13px">{{ $i+1 }}</td>
        <td>
          <div style="font-weight:600;color:var(--gray-900)">{{ $item->nama }}</div>
          <div style="font-size:11px;color:var(--gray-400)">{{ $item->created_at->format('d M Y') }}</div>
        </td>
        <td class="tm">{{ $item->no_hp ?: '—' }}</td>
        <td class="tm" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $item->alamat ?: '—' }}</td>
        <td class="tm">{{ $item->catatan ? \Str::limit($item->catatan,35) : '—' }}</td>
        <td>
          <div style="display:flex;gap:6px">
            <button class="btn btn-secondary btn-sm" onclick="editPelanggan({{ $item->id }},'{{ addslashes($item->nama) }}','{{ $item->no_hp }}','{{ addslashes($item->alamat) }}','{{ addslashes($item->catatan) }}')">✏️</button>
            <form method="POST" action="{{ route('pelanggan.destroy',$item) }}" onsubmit="return confirm('Hapus pelanggan {{ $item->nama }}?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm" type="submit">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6">
        <div class="empty">
          <div class="empty-ico">👥</div>
          <p class="empty-ttl">{{ $search ? "Tidak ada hasil untuk \"$search\"" : 'Belum ada pelanggan terdaftar' }}</p>
          @if(!$search)<button class="btn btn-primary btn-sm" onclick="openModal('m-add')">+ Tambah Sekarang</button>@endif
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="modal-overlay" id="m-add">
  <div class="modal">
    <div class="modal-header"><h3 class="modal-title">Tambah Pelanggan</h3><button class="modal-close" onclick="closeModal('m-add')">✕</button></div>
    <div class="modal-body">
      <form method="POST" action="{{ route('pelanggan.store') }}">
        @csrf
        <div class="form-group"><label class="form-label">Nama *</label><input type="text" name="nama" class="form-control" placeholder="Nama lengkap pelanggan" required></div>
        <div class="form-group"><label class="form-label">No. HP</label><input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789"></div>
        <div class="form-group"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea></div>
        <div class="form-group"><label class="form-label">Catatan</label><textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea></div>
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
    <div class="modal-header"><h3 class="modal-title">Edit Pelanggan</h3><button class="modal-close" onclick="closeModal('m-edit')">✕</button></div>
    <div class="modal-body">
      <form method="POST" id="form-edit-pl">
        @csrf @method('PUT')
        <div class="form-group"><label class="form-label">Nama *</label><input type="text" name="nama" id="pl-nama" class="form-control" required></div>
        <div class="form-group"><label class="form-label">No. HP</label><input type="text" name="no_hp" id="pl-nohp" class="form-control"></div>
        <div class="form-group"><label class="form-label">Alamat</label><textarea name="alamat" id="pl-alamat" class="form-control" rows="2"></textarea></div>
        <div class="form-group"><label class="form-label">Catatan</label><textarea name="catatan" id="pl-catatan" class="form-control" rows="2"></textarea></div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
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
function editPelanggan(id,nama,nohp,alamat,catatan){
  document.getElementById('form-edit-pl').action='/pelanggan/'+id;
  document.getElementById('pl-nama').value=nama;
  document.getElementById('pl-nohp').value=nohp;
  document.getElementById('pl-alamat').value=alamat;
  document.getElementById('pl-catatan').value=catatan;
  openModal('m-edit');
}
</script>
@endpush
