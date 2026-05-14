@extends('layouts.app')
@section('title','Pengaturan')
@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Pengaturan</h1>
    <p class="page-sub">Kelola konfigurasi sistem dan Telegram Bot</p>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px">

  {{-- Telegram Bot Info --}}
  <div class="card">
    <div class="card-body">
      <h2 style="font-size:15px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px">
        <span style="font-size:20px">🤖</span> Telegram Bot
      </h2>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="padding:12px 14px;background:var(--gray-50);border-radius:var(--rmd);border:1px solid var(--gray-200);position:relative">
          <p style="font-size:12px;color:var(--gray-500);font-weight:600;margin-bottom:4px">BOT TOKEN</p>
          <div style="display:flex;align-items:center;justify-content:space-between;gap:10px">
            <p id="botTokenText" style="font-size:14px;font-family:var(--mono);color:var(--gray-700);word-break:break-all">{{ $botToken }}</p>
            @if($rawToken)
            <button onclick="toggleToken()" style="background:none;border:none;cursor:pointer;color:var(--gray-400);display:flex;padding:4px;border-radius:4px;transition:all 0.2s" onmouseover="this.style.background='var(--gray-100)';this.style.color='var(--gray-600)'" onmouseout="this.style.background='none';this.style.color='var(--gray-400)'" title="Show/Hide Token">
              <svg id="eyeIcon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path id="eyePathOuter" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
            @endif
          </div>
        </div>
        <div style="padding:12px 14px;background:var(--gray-50);border-radius:var(--rmd);border:1px solid var(--gray-200)">
          <p style="font-size:12px;color:var(--gray-500);font-weight:600;margin-bottom:6px">ALLOWED CHAT IDs (Admin)</p>
          @forelse($allowedIds as $id)
            <span class="badge badge-green" style="margin-right:4px;margin-bottom:4px;font-family:var(--mono)">{{ $id }}</span>
          @empty
            <span style="font-size:13px;color:var(--gray-400)">Belum ada Chat ID yang diset di .env</span>
          @endforelse
        </div>
        <div style="padding:12px 14px;background:var(--green-50);border-radius:var(--rmd);border:1px solid var(--green-100)">
          <p style="font-size:12px;font-weight:600;color:var(--green-700);margin-bottom:6px">PERINTAH BOT</p>
          <div style="display:flex;flex-direction:column;gap:3px;font-size:13px;color:var(--gray-600)">
            <div><code style="background:#fff;padding:1px 6px;border-radius:4px">/rekap</code> — Rekap bulan ini</div>
            <div><code style="background:#fff;padding:1px 6px;border-radius:4px">/rekap harian</code> — Rekap hari ini</div>
            <div><code style="background:#fff;padding:1px 6px;border-radius:4px">/rekap tahunan</code> — Rekap tahun ini</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- App Info --}}
  <div class="card">
    <div class="card-body">
      <h2 style="font-size:15px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px">
        <span style="font-size:20px">ℹ️</span> Informasi Sistem
      </h2>
      <div style="display:flex;flex-direction:column;gap:10px">
        @php
          $infos = [
            ['label'=>'Aplikasi',   'val'=>config('app.name')],
            ['label'=>'Versi PHP',  'val'=>PHP_VERSION],
            ['label'=>'Framework',  'val'=>'Laravel '.app()->version()],
            ['label'=>'Environment','val'=>config('app.env')],
            ['label'=>'Timezone',   'val'=>config('app.timezone')],
            ['label'=>'Database',   'val'=>config('database.default').' — '.config('database.connections.pgsql.host')],
          ];
        @endphp
        @foreach($infos as $info)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--gray-100)">
          <span style="font-size:13px;color:var(--gray-500);font-weight:600">{{ $info['label'] }}</span>
          <span style="font-size:13px;color:var(--gray-700);font-family:var(--mono)">{{ $info['val'] }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>

{{-- Telegram Subscribers --}}
<div class="card" style="margin-top:20px">
  <div class="card-body">
    <h2 style="font-size:15px;font-weight:700;margin-bottom:4px;display:flex;align-items:center;gap:8px">
      <span style="font-size:20px">📡</span> Subscriber Telegram
    </h2>
    <p style="font-size:13px;color:var(--gray-400);margin-bottom:16px">
      Daftar pengguna yang pernah berinteraksi dengan bot. Toggle aktif/nonaktif untuk mengontrol akses notifikasi.
    </p>

    @if($subscribers->isEmpty())
      <div class="empty" style="padding:32px">
        <div class="empty-ico">📡</div>
        <p class="empty-ttl">Belum ada subscriber</p>
        <p class="empty-sub">Kirim pesan ke bot untuk mulai terdaftar sebagai subscriber</p>
      </div>
    @else
    <div class="table-wrap">
      <table>
        <thead><tr><th>Chat ID</th><th>Username</th><th>Nama</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          @foreach($subscribers as $sub)
          <tr>
            <td><span class="mono" style="font-size:13px">{{ $sub->chat_id }}</span></td>
            <td class="tm">{{ $sub->username ? '@'.$sub->username : '—' }}</td>
            <td>{{ trim(($sub->first_name ?? '').' '.($sub->last_name ?? '')) ?: '—' }}</td>
            <td>
              <span class="badge {{ $sub->is_active ? 'badge-green' : 'badge-red' }}">
                {{ $sub->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
              </span>
            </td>
            <td>
              <form method="POST" action="{{ route('settings.subscriber.toggle',$sub) }}">
                @csrf
                <button class="btn btn-secondary btn-sm" type="submit">
                  {{ $sub->is_active ? '🔴 Nonaktifkan' : '🟢 Aktifkan' }}
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  let isTokenVisible = false;
  const maskedToken = "{{ $botToken }}";
  const rawToken    = "{{ $rawToken }}";

  function toggleToken() {
    isTokenVisible = !isTokenVisible;
    const textEl = document.getElementById('botTokenText');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (isTokenVisible) {
      textEl.innerText = rawToken;
      // Change icon to "eye-off"
      eyeIcon.innerHTML = `
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
      `;
    } else {
      textEl.innerText = maskedToken;
      // Change icon back to "eye"
      eyeIcon.innerHTML = `
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
      `;
    }
  }
</script>
@endpush
