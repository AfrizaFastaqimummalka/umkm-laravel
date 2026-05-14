@extends('layouts.app')
@section('title','Laporan')
@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Laporan Keuangan</h1>
    <p class="page-sub">Lihat dan export laporan berdasarkan periode</p>
  </div>
</div>

<div class="card" style="margin-bottom:24px">
  <div class="card-body">
    <form method="GET" action="{{ route('laporan.generate') }}" id="form-lap">
      <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">
        @php $ct=request('tipe',$tipe??'bulanan'); @endphp
        @foreach(['harian'=>'Harian','bulanan'=>'Bulanan','tahunan'=>'Tahunan'] as $k=>$l)
          <button type="button" class="ptab {{ $ct===$k?'active':'' }}" onclick="setTipe('{{ $k }}')">{{ $l }}</button>
        @endforeach
        <input type="hidden" name="tipe" id="inp-tipe" value="{{ $ct }}">
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        <div id="f-harian" style="{{ $ct==='harian'?'':'display:none' }}">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal',date('Y-m-d')) }}" style="width:180px">
          </div>
        </div>
        <div id="f-bulanan" style="{{ $ct==='bulanan'?'display:flex':'display:none' }};gap:10px">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-control">
              @foreach(['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12] as $n=>$v)
                <option value="{{ $v }}" {{ request('bulan',date('n'))==$v?'selected':'' }}>{{ $n }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-control">
              @foreach([2023,2024,2025,2026] as $y)
                <option value="{{ $y }}" {{ request('tahun',date('Y'))==$y?'selected':'' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div id="f-tahunan" style="{{ $ct==='tahunan'?'':'display:none' }}">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-control">
              @foreach([2023,2024,2025,2026] as $y)
                <option value="{{ $y }}" {{ request('tahun',date('Y'))==$y?'selected':'' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div style="padding-bottom:0">
          <button type="submit" class="btn btn-primary">📊 Lihat Laporan</button>
        </div>
      </div>
    </form>
  </div>
</div>

@isset($totalMasuk)
{{-- Summary --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px">
  @php
  $sc=[
    ['label'=>'Total Pemasukan',  'val'=>$totalMasuk,  'color'=>'#0F6E56','bg'=>'#E8F8F2','icon'=>'💰'],
    ['label'=>'Total Pengeluaran','val'=>$totalKeluar, 'color'=>'#DC2626','bg'=>'#FEF2F2','icon'=>'💸'],
    ['label'=>'Saldo Bersih',     'val'=>$saldo,        'color'=>$saldo>=0?'#0F6E56':'#DC2626','bg'=>$saldo>=0?'#E8F8F2':'#FEF2F2','icon'=>'🏦'],
  ];
  @endphp
  @foreach($sc as $s)
  <div class="stat-card">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
      <span style="font-size:20px">{{ $s['icon'] }}</span>
      <span style="font-size:12px;font-weight:600;color:var(--gray-500);text-transform:uppercase;letter-spacing:.04em">{{ $s['label'] }}</span>
    </div>
    <p class="mono" style="font-size:18px;color:{{ $s['color'] }}">Rp {{ number_format($s['val'],0,',','.') }}</p>
  </div>
  @endforeach
</div>

{{-- Export --}}
<div style="display:flex;justify-content:flex-end;margin-bottom:16px">
  <a href="{{ route('laporan.export',request()->all()) }}" class="btn btn-secondary">
    📥 Export Excel (.xlsx)
  </a>
</div>

{{-- Tabel Pemasukan --}}
<div style="margin-bottom:28px">
  <h3 style="font-size:15px;font-weight:700;color:var(--gray-800);margin-bottom:12px">
    Detail Pemasukan
    <span style="font-size:13px;font-weight:500;color:var(--gray-400);margin-left:6px">({{ $pemasukan->count() }} transaksi)</span>
  </h3>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th style="text-align:right">Jumlah</th></tr></thead>
      <tbody>
        @forelse($pemasukan as $item)
        <tr>
          <td style="white-space:nowrap">{{ $item->tanggal->format('d M Y') }}</td>
          <td><span class="badge badge-green">{{ str_replace('_',' ',ucfirst($item->kategori)) }}</span></td>
          <td class="tm">{{ $item->keterangan ?: '—' }}</td>
          <td style="text-align:right"><span class="mono tg">Rp {{ number_format($item->jumlah,0,',','.') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--gray-400)">Tidak ada pemasukan di periode ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Tabel Pengeluaran --}}
<div>
  <h3 style="font-size:15px;font-weight:700;color:var(--gray-800);margin-bottom:12px">
    Detail Pengeluaran
    <span style="font-size:13px;font-weight:500;color:var(--gray-400);margin-left:6px">({{ $pengeluaran->count() }} transaksi)</span>
  </h3>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th style="text-align:right">Jumlah</th></tr></thead>
      <tbody>
        @forelse($pengeluaran as $item)
        <tr>
          <td style="white-space:nowrap">{{ $item->tanggal->format('d M Y') }}</td>
          <td><span class="badge badge-amber">{{ str_replace('_',' ',ucfirst($item->kategori)) }}</span></td>
          <td class="tm">{{ $item->keterangan ?: '—' }}</td>
          <td style="text-align:right"><span class="mono tr">Rp {{ number_format($item->jumlah,0,',','.') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--gray-400)">Tidak ada pengeluaran di periode ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@else
<div style="text-align:center;padding:60px 20px;color:var(--gray-400)">
  <div style="font-size:40px;margin-bottom:12px">📊</div>
  <p style="font-weight:600;color:var(--gray-600);margin-bottom:4px">Pilih periode dan klik "Lihat Laporan"</p>
  <p style="font-size:13px">Data laporan akan ditampilkan di sini</p>
</div>
@endisset

@endsection
@push('scripts')
<script>
function setTipe(t){
  document.getElementById('inp-tipe').value=t;
  ['harian','bulanan','tahunan'].forEach(x=>{
    const el=document.getElementById('f-'+x);
    el.style.display=x===t?(t==='bulanan'?'flex':''):'none';
  });
  document.querySelectorAll('.ptab').forEach((b,i)=>{
    b.classList.toggle('active',['harian','bulanan','tahunan'][i]===t);
  });
}
</script>
@endpush
