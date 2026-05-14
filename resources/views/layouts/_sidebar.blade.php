<div class="sb-brand">
  <div class="sb-icon" style="background:transparent;overflow:hidden;">
    <img src="/logo.png" alt="Logo" style="width:100%;height:100%;object-fit:cover;">
  </div>
  <div><div class="sb-name">Pabrik Tempe<br>Pak Iwan</div><div class="sb-sub">Admin</div></div>
</div>
<div class="sb-div"></div>
<nav class="sb-nav">
  <div class="sb-lbl">Menu Utama</div>
  @php
  $nav=[
    ['route'=>'dashboard',        'label'=>'Dashboard',   'icon'=>'📊'],
    ['route'=>'inventori.index',  'label'=>'Inventori',   'icon'=>'📦'],
    ['route'=>'pemasukan.index',  'label'=>'Pemasukan',   'icon'=>'💰'],
    ['route'=>'pengeluaran.index','label'=>'Pengeluaran', 'icon'=>'💸'],
    ['route'=>'pelanggan.index',  'label'=>'Pelanggan',   'icon'=>'👥'],
    ['route'=>'pemasok.index',    'label'=>'Pemasok',     'icon'=>'🚛'],
    ['route'=>'laporan.index',    'label'=>'Laporan',     'icon'=>'📋'],
    ['route'=>'settings.index',   'label'=>'Pengaturan',  'icon'=>'⚙️'],
  ];
  @endphp
  @foreach($nav as $n)
  <a href="{{ route($n['route']) }}"
     class="nav-item {{ request()->routeIs($n['route']) ? 'active' : '' }}"
     onclick="closeSidebar()">
    <span style="font-size:16px;width:20px;text-align:center;flex-shrink:0">{{ $n['icon'] }}</span>
    {{ $n['label'] }}
  </a>
  @endforeach
</nav>
<div class="sb-foot">
  <div class="sb-div"></div>
  <div style="padding:0 12px 8px;font-size:12px;color:var(--gray-400)">
    Login sebagai: <strong style="color:var(--gray-600)">{{ auth()->user()->name ?? 'Admin' }}</strong>
  </div>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button class="btn-out" type="submit">
      <span style="font-size:16px">🚪</span> Keluar
    </button>
  </form>
</div>
