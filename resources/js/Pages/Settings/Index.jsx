import React from 'react';
import AppLayout from '../../Layouts/AppLayout';
import { Head, router } from '@inertiajs/react';

export default function Index({ subscribers, telegram_bot_token, allowed_ids }) {
    
    const toggleStatus = (id) => {
        router.post(route('settings.subscriber.toggle', id));
    };

    const runMigrate = () => {
        if(confirm('Jalankan migrasi database sekarang?')) {
            router.post(route('settings.migrate'));
        }
    };

    return (
        <AppLayout>
            <Head title="Pengaturan Sistem" />
            
            <div className="page-header">
                <div>
                    <h1 className="page-title">Pengaturan Sistem</h1>
                    <p className="page-sub">Konfigurasi & Manajemen Akses</p>
                </div>
            </div>

            <div style={{display:'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(400px, 1fr))', gap:'20px'}}>
                {/* Info Telegram */}
                <div className="card">
                    <div className="card-body">
                        <h3 className="form-label mb-4">🤖 Integrasi Telegram Bot</h3>
                        <div className="form-group">
                            <label className="form-label">Bot Token Status</label>
                            <p className="mono tg">{telegram_bot_token}</p>
                        </div>
                        <div className="form-group">
                            <label className="form-label">Allowed Chat IDs (Config)</label>
                            <p className="tm">{allowed_ids.join(', ') || 'None'}</p>
                        </div>
                        <p className="tm" style={{fontSize:'12px'}}>* Edit file `.env` di server untuk mengubah Token atau ID Admin Utama.</p>
                    </div>
                </div>

                {/* Alat Hosting */}
                <div className="card">
                    <div className="card-body">
                        <h3 className="form-label mb-4">🚀 Alat Maintenance (Shared Hosting)</h3>
                        <div className="form-group">
                            <label className="form-label">Migrasi Database</label>
                            <button className="btn btn-secondary btn-sm" onClick={runMigrate}>
                                🔄 Jalankan php artisan migrate
                            </button>
                        </div>
                        <p className="tm" style={{fontSize:'12px'}}>Gunakan ini jika Anda tidak memiliki akses SSH di cPanel untuk menjalankan migrasi.</p>
                    </div>
                </div>
            </div>

            <div className="card mt-6">
                <div className="card-body">
                    <h3 className="form-label mb-4">👥 Manajemen Subscriber Bot</h3>
                    <div className="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Chat ID</th>
                                    <th>Status</th>
                                    <th style={{textAlign:'right'}}>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {subscribers.map((sub) => (
                                    <tr key={sub.id}>
                                        <td style={{fontWeight:600}}>{sub.first_name} {sub.last_name}</td>
                                        <td className="tm">@{sub.username || '—'}</td>
                                        <td className="mono">{sub.chat_id}</td>
                                        <td>
                                            <span className={`badge ${sub.is_active ? 'badge-green' : 'badge-red'}`}>
                                                {sub.is_active ? 'Aktif' : 'Nonaktif'}
                                            </span>
                                        </td>
                                        <td style={{textAlign:'right'}}>
                                            <button 
                                                className={`btn btn-sm ${sub.is_active ? 'btn-danger' : 'btn-primary'}`}
                                                onClick={() => toggleStatus(sub.id)}
                                            >
                                                {sub.is_active ? 'Blokir' : 'Aktifkan'}
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                                {subscribers.length === 0 && (
                                    <tr>
                                        <td colSpan="5" className="tm" style={{textAlign:'center', padding:'30px'}}>
                                            Belum ada pengguna yang berinteraksi dengan bot.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
