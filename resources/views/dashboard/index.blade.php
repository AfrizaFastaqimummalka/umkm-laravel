@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-sub">{{ $label }}</p>
  </div>
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
    <div class="ptabs">
      @foreach(['hari'=>'Hari Ini','bulan'=>'Bulan Ini','tahun'=>'Tahun Ini'] as $k=>$l)
        <a href="{{ route('dashboard',['periode'=>$k]) }}" class="ptab {{ $periode===$k?'active':'' }}">{{ $l }}</a>
      @endforeach
    </div>
    <a href="{{ route('dashboard',['periode'=>$periode]) }}" style="padding:6px 10px;border:1.5px solid var(--gray-200);border-radius:var(--rmd);display:flex;background:#fff;color:var(--gray-500)">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
    </a>
  </div>
</div>

{{-- Stat Cards --}}
<div class="stat-grid">
  @php
  $stats=[
    ['label'=>'Total Pemasukan',  'val'=>$totalMasuk,  'color'=>'#0F6E56','bg'=>'#E8F8F2','icon'=>'💰'],
    ['label'=>'Total Pengeluaran','val'=>$totalKeluar, 'color'=>'#DC2626','bg'=>'#FEF2F2','icon'=>'💸'],
    ['label'=>'Saldo Bersih',     'val'=>$saldo,        'color'=>$saldo>=0?'#0F6E56':'#DC2626','bg'=>$saldo>=0?'#E8F8F2':'#FEF2F2','icon'=>$saldo>=0?'✅':'⚠️'],
  ];
  @endphp
  @foreach($stats as $s)
  <div class="stat-card">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
      <div style="flex:1;min-width:0">
        <p style="font-size:12px;font-weight:600;color:var(--gray-500);text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">{{ $s['label'] }}</p>
        <p class="mono" style="font-size:20px;color:{{ $s['color'] }};line-height:1.2">Rp {{ number_format($s['val'],0,',','.') }}</p>
      </div>
      <div style="width:44px;height:44px;border-radius:12px;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">{{ $s['icon'] }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Chart --}}
<div class="card" style="margin-bottom:20px">
  <div class="card-body">
    <h2 style="font-size:15px;font-weight:700;color:var(--gray-800);margin-bottom:4px">Grafik 7 Hari Terakhir</h2>
    <p style="font-size:13px;color:var(--gray-400);margin-bottom:20px">Pemasukan vs pengeluaran harian</p>
    <canvas id="chart7" style="max-height:260px"></canvas>
    <div style="display:flex;gap:20px;margin-top:14px">
      <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--gray-500)"><div style="width:12px;height:12px;border-radius:3px;background:#1D9E75"></div>Pemasukan</div>
      <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--gray-500)"><div style="width:12px;height:12px;border-radius:3px;background:#EF4444"></div>Pengeluaran</div>
    </div>
  </div>
</div>

{{-- Shortcut --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(175px,1fr));gap:12px">
  @php
  $sc=[
    ['route'=>'pemasukan.index',  'label'=>'Tambah Pemasukan',  'icon'=>'💰','c'=>'var(--green-500)','bg'=>'var(--green-50)'],
    ['route'=>'pengeluaran.index','label'=>'Tambah Pengeluaran','icon'=>'💸','c'=>'var(--red-500)',  'bg'=>'var(--red-50)'],
    ['route'=>'laporan.index',    'label'=>'Lihat Laporan',     'icon'=>'📊','c'=>'#6366F1',        'bg'=>'#EEF2FF'],
    ['route'=>'pelanggan.index',  'label'=>'Data Pelanggan',    'icon'=>'👥','c'=>'#F59E0B',        'bg'=>'#FFFBEB'],
  ];
  @endphp
  @foreach($sc as $s)
  <a href="{{ route($s['route']) }}" style="display:flex;align-items:center;gap:10px;padding:14px 16px;background:#fff;border:1px solid var(--gray-100);border-radius:var(--rlg);box-shadow:0 1px 3px rgba(0,0,0,.06);transition:all .15s;font-size:14px;font-weight:600;color:var(--gray-700)"
    onmouseover="this.style.borderColor='{{ $s['c'] }}';this.style.transform='translateY(-1px)'"
    onmouseout="this.style.borderColor='var(--gray-100)';this.style.transform='none'">
    <div style="width:36px;height:36px;border-radius:9px;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0">{{ $s['icon'] }}</div>
    {{ $s['label'] }}
  </a>
  @endforeach
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const d = @json($chart);
new Chart(document.getElementById('chart7'),{
  type:'bar',
  data:{
    labels:d.map(x=>x.tanggal),
    datasets:[
      {label:'Pemasukan',  data:d.map(x=>x.pemasukan),  backgroundColor:'#1D9E75',borderRadius:6,borderSkipped:false},
      {label:'Pengeluaran',data:d.map(x=>x.pengeluaran),backgroundColor:'#EF4444',borderRadius:6,borderSkipped:false}
    ]
  },
  options:{
    responsive:true,
    plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>c.dataset.label+': Rp '+c.raw.toLocaleString('id-ID')}}},
    scales:{
      x:{grid:{display:false},ticks:{color:'#9CA3AF',font:{size:12}}},
      y:{grid:{color:'#F3F4F6'},ticks:{color:'#9CA3AF',font:{size:11},callback:v=>v>=1e6?(v/1e6).toFixed(0)+'jt':(v/1e3).toFixed(0)+'rb'}}
    }
  }
});
</script>
@endpush
