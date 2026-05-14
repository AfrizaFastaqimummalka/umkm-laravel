<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — UMKM Tempe</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--green-50:#E8F8F2;--green-500:#1D9E75;--green-600:#0F6E56;--gray-100:#F3F4F6;--gray-300:#D1D5DB;--gray-500:#6B7280;--gray-700:#374151;--gray-900:#111827;--red-50:#FEF2F2;--red-500:#EF4444;--red-600:#DC2626;--font:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif}
body{font-family:var(--font);background:linear-gradient(135deg,var(--green-50) 0%,#f0fdf9 60%,#fff 100%);min-height:100dvh;display:flex;-webkit-font-smoothing:antialiased}
.left{width:45%;background:var(--green-600);display:flex;flex-direction:column;justify-content:center;padding:60px 48px;position:relative;overflow:hidden}
.left::before{content:'';position:absolute;top:-80px;right:-80px;width:320px;height:320px;border-radius:50%;background:rgba(255,255,255,.06)}
.left::after{content:'';position:absolute;bottom:-60px;left:-60px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.04)}
.lc{position:relative;z-index:1}
.lc-emoji{font-size:48px;margin-bottom:20px}
.lc-title{font-size:30px;font-weight:800;color:#fff;line-height:1.2;margin-bottom:12px}
.lc-desc{font-size:15px;color:rgba(255,255,255,.75);line-height:1.7;max-width:340px;margin-bottom:36px}
.features{display:flex;flex-direction:column;gap:10px}
.feat{display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.9);font-size:14px}
.feat-dot{width:20px;height:20px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;color:#fff}
.right{flex:1;display:flex;align-items:center;justify-content:center;padding:24px 20px}
.box{width:100%;max-width:380px}
.card{background:#fff;border-radius:16px;padding:36px 32px;box-shadow:0 8px 32px rgba(0,0,0,.08);border:1px solid var(--gray-100)}
.card-icon{width:46px;height:46px;border-radius:12px;background:var(--green-50);display:flex;align-items:center;justify-content:center;margin-bottom:16px;font-size:22px}
.card-title{font-size:20px;font-weight:700;color:var(--gray-900);margin-bottom:4px}
.card-sub{font-size:13px;color:var(--gray-500);margin-bottom:28px}
.fg{margin-bottom:18px}
.fl{display:block;font-size:13px;font-weight:600;color:var(--gray-700);margin-bottom:5px}
.fi{width:100%;padding:10px 14px;border-radius:6px;border:1.5px solid var(--gray-300);font-size:14px;font-family:inherit;color:var(--gray-900);background:#fff;transition:border .15s;outline:none}
.fi:focus{border-color:var(--green-500)}
.pw{position:relative}
.pt{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray-500);padding:0;font-size:16px;line-height:1}
.sub{width:100%;padding:11px;border-radius:10px;background:var(--green-500);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;transition:background .15s;margin-top:4px}
.sub:hover{background:var(--green-600)}
.err{background:var(--red-50);border:1px solid var(--red-500);border-radius:6px;padding:10px 14px;font-size:13px;color:var(--red-600);margin-bottom:20px;font-weight:500}
.foot{text-align:center;font-size:12px;color:var(--gray-500);margin-top:20px}
@media(max-width:768px){.left{display:none}}
</style>
</head>
<body>
<div class="left">
  <div class="lc">
    <div class="lc-emoji">🫘</div>
    <h1 class="lc-title">UMKM Tempe<br>Manajemen Keuangan</h1>
    <p class="lc-desc">Kelola pemasukan, pengeluaran, dan laporan keuangan usaha tempe Anda dengan mudah dan terstruktur.</p>
    <div class="features">
      @foreach(['Laporan harian, bulanan & tahunan','Export Excel otomatis','Rekap via Telegram Bot','Data pelanggan terorganisir'] as $f)
      <div class="feat"><div class="feat-dot">✓</div>{{ $f }}</div>
      @endforeach
    </div>
  </div>
</div>
<div class="right">
  <div class="box">
    <div class="card">
      <div class="card-icon">🔐</div>
      <h2 class="card-title">Masuk ke Sistem</h2>
      <p class="card-sub">Masukkan kredensial admin Anda</p>
      @if($errors->any())
        <div class="err">⚠️ {{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="fg">
          <label class="fl" for="username">Username</label>
          <input class="fi" type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
        </div>
        <div class="fg">
          <label class="fl" for="password">Password</label>
          <div class="pw">
            <input class="fi" type="password" id="password" name="password" placeholder="Masukkan password" required style="padding-right:42px">
            <button type="button" class="pt" onclick="this.previousElementSibling.type=this.previousElementSibling.type==='password'?'text':'password'">👁</button>
          </div>
        </div>
        <button type="submit" class="sub">Masuk</button>
      </form>
    </div>
    <p class="foot">Sistem Manajemen Keuangan UMKM Tempe © {{ date('Y') }}</p>
  </div>
</div>
</body>
</html>
