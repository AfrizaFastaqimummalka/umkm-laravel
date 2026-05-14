<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Dashboard') — Pabrik Tempe Pak Iwan</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --green-50:#E8F8F2;--green-500:#1D9E75;--green-600:#0F6E56;--green-700:#085041;
  --gray-50:#F9FAFB;--gray-100:#F3F4F6;--gray-200:#E5E7EB;--gray-300:#D1D5DB;
  --gray-400:#9CA3AF;--gray-500:#6B7280;--gray-600:#4B5563;--gray-700:#374151;--gray-900:#111827;
  --red-50:#FEF2F2;--red-500:#EF4444;--red-600:#DC2626;
  --amber-50:#FFFBEB;--amber-500:#F59E0B;
  --font:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;
  --mono:'Consolas','Courier New',monospace;
  --sidebar:240px;--rsm:6px;--rmd:10px;--rlg:16px;
}
html{font-size:16px}
body{font-family:var(--font);background:var(--gray-50);color:var(--gray-900);-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
button,input,select,textarea{font-family:var(--font)}
::-webkit-scrollbar{width:5px;height:5px}::-webkit-scrollbar-thumb{background:var(--gray-300);border-radius:3px}

/* Layout */
.wrap{display:flex;min-height:100dvh}
.sidebar{width:var(--sidebar);flex-shrink:0;background:#fff;border-right:1px solid var(--gray-200);display:flex;flex-direction:column;position:sticky;top:0;height:100dvh;overflow-y:auto;z-index:20}
.main{flex:1;display:flex;flex-direction:column;min-width:0}
.page{flex:1;padding:24px 28px;max-width:1200px;width:100%;margin:0 auto}

/* Sidebar */
.sb-brand{padding:22px 20px 14px;display:flex;align-items:center;gap:10px}
.sb-icon{width:36px;height:36px;border-radius:10px;background:var(--green-500);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.sb-name{font-weight:700;font-size:14px;color:var(--gray-900);line-height:1.2}
.sb-sub{font-size:11px;color:var(--gray-500)}
.sb-div{height:1px;background:var(--gray-100);margin:0 16px 10px}
.sb-nav{flex:1;padding:0 10px}
.sb-lbl{font-size:10px;font-weight:700;color:var(--gray-400);letter-spacing:.08em;text-transform:uppercase;padding:8px 10px 4px}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:var(--rmd);margin-bottom:2px;font-size:14px;font-weight:500;color:var(--gray-600);transition:all .15s;position:relative}
.nav-item:hover{background:var(--gray-50);color:var(--gray-900)}
.nav-item.active{background:var(--green-50);color:var(--green-700);font-weight:600}
.nav-item.active::before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);width:3px;height:20px;background:var(--green-500);border-radius:0 3px 3px 0}
.nav-item svg{flex-shrink:0;opacity:.7}
.nav-item.active svg,.nav-item:hover svg{opacity:1}
.sb-foot{padding:10px}
.btn-out{display:flex;align-items:center;gap:10px;width:100%;padding:9px 12px;border-radius:var(--rmd);font-size:14px;font-weight:500;color:var(--gray-500);background:transparent;border:none;cursor:pointer;transition:all .15s}
.btn-out:hover{background:var(--red-50);color:var(--red-600)}

/* Mobile topbar */
.topbar{display:none;align-items:center;gap:12px;padding:12px 16px;background:#fff;border-bottom:1px solid var(--gray-200);position:sticky;top:0;z-index:30}
.menu-btn{background:none;border:none;cursor:pointer;padding:4px;display:flex;color:var(--gray-700)}
.topbar-brand{font-weight:700;font-size:15px}
.drawer-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:40}
.drawer{position:fixed;left:-260px;top:0;width:240px;height:100dvh;background:#fff;border-right:1px solid var(--gray-200);display:flex;flex-direction:column;z-index:50;transition:left .25s ease;overflow-y:auto}
.drawer.open{left:0}

/* Reusables */
.card{background:#fff;border-radius:var(--rlg);border:1px solid var(--gray-100);box-shadow:0 1px 3px rgba(0,0,0,.06)}
.card-body{padding:20px 24px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:var(--rmd);font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .15s;text-decoration:none;white-space:nowrap}
.btn-primary{background:var(--green-500);color:#fff}.btn-primary:hover{background:var(--green-600)}
.btn-secondary{background:#fff;color:var(--gray-700);border:1.5px solid var(--gray-300)}.btn-secondary:hover{background:var(--gray-50)}
.btn-danger{background:var(--red-500);color:#fff}.btn-danger:hover{background:var(--red-600)}
.btn-sm{padding:6px 12px;font-size:13px}
.form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:16px}
.form-label{font-size:13px;font-weight:600;color:var(--gray-700)}
.form-control{padding:9px 12px;border-radius:var(--rsm);border:1.5px solid var(--gray-300);font-size:14px;color:var(--gray-900);background:#fff;width:100%;transition:border .15s;outline:none}
.form-control:focus{border-color:var(--green-500)}
.table-wrap{overflow-x:auto;border-radius:var(--rmd);border:1px solid var(--gray-200)}
table{width:100%;border-collapse:collapse;font-size:14px}
thead tr{background:var(--gray-50)}
th{padding:10px 14px;text-align:left;font-weight:600;font-size:12px;color:var(--gray-500);letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid var(--gray-200);white-space:nowrap}
td{padding:11px 14px;color:var(--gray-700);border-bottom:1px solid var(--gray-100);vertical-align:middle}
tbody tr:hover{background:var(--gray-50)}
tbody tr:last-child td{border-bottom:none}
.badge{display:inline-block;padding:3px 9px;border-radius:999px;font-size:12px;font-weight:600}
.badge-green{background:var(--green-50);color:var(--green-700)}
.badge-amber{background:var(--amber-50);color:#92400E}
.badge-gray{background:var(--gray-100);color:var(--gray-600)}
.badge-red{background:var(--red-50);color:var(--red-600)}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100;align-items:center;justify-content:center;padding:16px}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:var(--rlg);width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.15);max-height:90dvh;display:flex;flex-direction:column}
.modal-header{display:flex;align-items:center;justify-content:space-between;padding:18px 20px 14px;border-bottom:1px solid var(--gray-100)}
.modal-title{font-size:16px;font-weight:700}
.modal-close{background:none;border:none;cursor:pointer;color:var(--gray-400);padding:4px;font-size:18px;line-height:1;border-radius:6px}
.modal-body{padding:20px;overflow-y:auto}
.alert{padding:10px 14px;border-radius:var(--rmd);font-size:13px;font-weight:500;margin-bottom:16px}
.alert-success{background:var(--green-50);color:var(--green-700);border:1px solid #A7F3D0}
.alert-error{background:var(--red-50);color:var(--red-600);border:1px solid #FECACA}
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap}
.page-title{font-size:22px;font-weight:700}
.page-sub{font-size:14px;color:var(--gray-500);margin-top:2px}
.filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:center}
.filter-bar label{font-size:13px;font-weight:600;color:var(--gray-600)}
.fsel{padding:7px 10px;border-radius:var(--rsm);border:1.5px solid var(--gray-300);font-size:13px;cursor:pointer;background:#fff;color:var(--gray-900);outline:none}
.mono{font-family:var(--mono);font-weight:600}
.tg{color:var(--green-600)}.tr{color:var(--red-600)}.tm{color:var(--gray-400)}
.ptabs{display:flex;gap:6px;flex-wrap:wrap}
.ptab{padding:6px 14px;border-radius:var(--rmd);font-size:13px;font-weight:600;cursor:pointer;border:1.5px solid var(--gray-200);background:#fff;color:var(--gray-500);transition:all .15s}
.ptab.active{background:var(--green-500);color:#fff;border-color:var(--green-500)}
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px}
.stat-card{background:#fff;border-radius:var(--rlg);padding:20px 22px;border:1px solid var(--gray-100);box-shadow:0 1px 3px rgba(0,0,0,.06)}
.empty{text-align:center;padding:48px 20px}
.empty-ico{font-size:36px;margin-bottom:12px}
.empty-ttl{font-weight:600;color:var(--gray-600);margin-bottom:4px}
.empty-sub{font-size:13px;color:var(--gray-400);margin-bottom:16px}

@media(max-width:768px){
  .sidebar{display:none}
  .topbar{display:flex}
  .page{padding:16px}
  .stat-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:480px){.stat-grid{grid-template-columns:1fr}}
</style>
@stack('styles')
</head>
<body>
<div class="wrap">

  <aside class="sidebar">@include('layouts._sidebar')</aside>

  <div class="drawer-overlay" id="ov" onclick="closeSidebar()"></div>
  <div class="drawer" id="drawer">@include('layouts._sidebar')</div>

  <main class="main">
    <div class="topbar">
      <button class="menu-btn" onclick="openSidebar()">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <span class="topbar-brand" style="display:flex;align-items:center;gap:8px;"><img src="/logo.png" alt="Logo" style="width:24px;height:24px;object-fit:cover;border-radius:4px;"> Pabrik Tempe Pak Iwan</span>
    </div>

    <div class="page">
      @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
      @endif
      @yield('content')
    </div>
  </main>
</div>

@stack('scripts')
<script>
function openSidebar(){document.getElementById('drawer').classList.add('open');document.getElementById('ov').style.display='block'}
function closeSidebar(){document.getElementById('drawer').classList.remove('open');document.getElementById('ov').style.display='none'}
document.querySelectorAll('.modal-overlay').forEach(o=>o.addEventListener('click',e=>{if(e.target===o)o.classList.remove('open')}));
function openModal(id){document.getElementById(id).classList.add('open')}
function closeModal(id){document.getElementById(id).classList.remove('open')}
</script>
</body>
</html>
