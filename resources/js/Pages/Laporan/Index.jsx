import React from 'react';
import AppLayout from '../../Layouts/AppLayout';
import { Head, router } from '@inertiajs/react';

export default function Index({ pemasukan, pengeluaran, totalMasuk, totalKeluar, saldo, label, tipe, filters }) {
    
    const handleFilterChange = (field, value) => {
        router.get(route('laporan.index'), { ...filters, [field]: value }, { preserveState: true });
    };

    const handleExport = () => {
        const url = route('laporan.export', filters);
        window.open(url, '_blank');
    };

    return (
        <AppLayout>
            <Head title="Laporan Keuangan" />
            
            <div className="page-header">
                <div>
                    <h1 className="page-title">Laporan Keuangan</h1>
                    <p className="page-sub">Periode: {label}</p>
                </div>
                <button className="btn btn-primary" onClick={handleExport}>
                    📥 Export Excel
                </button>
            </div>

            <div className="filter-bar">
                <div className="form-group mb-0" style={{flexDirection:'row', alignItems:'center', gap:'10px'}}>
                    <label className="form-label mb-0">Tipe:</label>
                    <select className="fsel" value={tipe} onChange={e => handleFilterChange('tipe', e.target.value)}>
                        <option value="harian">Harian</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="tahunan">Tahunan</option>
                    </select>
                </div>

                {tipe === 'harian' && (
                    <div className="form-group mb-0" style={{flexDirection:'row', alignItems:'center', gap:'10px'}}>
                        <label className="form-label mb-0">Tanggal:</label>
                        <input type="date" className="fsel" value={filters.tanggal || ''} onChange={e => handleFilterChange('tanggal', e.target.value)} />
                    </div>
                )}

                {tipe === 'bulanan' && (
                    <>
                        <div className="form-group mb-0" style={{flexDirection:'row', alignItems:'center', gap:'10px'}}>
                            <label className="form-label mb-0">Bulan:</label>
                            <select className="fsel" value={filters.bulan || new Date().getMonth() + 1} onChange={e => handleFilterChange('bulan', e.target.value)}>
                                {['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'].map((m,i) => (
                                    <option key={i+1} value={i+1}>{m}</option>
                                ))}
                            </select>
                        </div>
                        <div className="form-group mb-0" style={{flexDirection:'row', alignItems:'center', gap:'10px'}}>
                            <label className="form-label mb-0">Tahun:</label>
                            <select className="fsel" value={filters.tahun || new Date().getFullYear()} onChange={e => handleFilterChange('tahun', e.target.value)}>
                                {[2023,2024,2025,2026].map(y => <option key={y} value={y}>{y}</option>)}
                            </select>
                        </div>
                    </>
                )}

                {tipe === 'tahunan' && (
                    <div className="form-group mb-0" style={{flexDirection:'row', alignItems:'center', gap:'10px'}}>
                        <label className="form-label mb-0">Tahun:</label>
                        <select className="fsel" value={filters.tahun || new Date().getFullYear()} onChange={e => handleFilterChange('tahun', e.target.value)}>
                            {[2023,2024,2025,2026].map(y => <option key={y} value={y}>{y}</option>)}
                        </select>
                    </div>
                )}
            </div>

            <div className="stat-grid">
                <div className="stat-card">
                    <h3 className="form-label" style={{color:'var(--green-600)'}}>Pemasukan</h3>
                    <p className="mono tg" style={{fontSize:'20px'}}>Rp {totalMasuk.toLocaleString('id-ID')}</p>
                </div>
                <div className="stat-card">
                    <h3 className="form-label" style={{color:'var(--red-600)'}}>Pengeluaran</h3>
                    <p className="mono tr" style={{fontSize:'20px'}}>Rp {totalKeluar.toLocaleString('id-ID')}</p>
                </div>
                <div className="stat-card">
                    <h3 className="form-label">Saldo Bersih</h3>
                    <p className="mono" style={{fontSize:'20px'}}>Rp {saldo.toLocaleString('id-ID')}</p>
                </div>
            </div>

            <div style={{display:'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(450px, 1fr))', gap:'20px'}}>
                {/* Tabel Pemasukan */}
                <div className="card">
                    <div className="card-body">
                        <h3 className="form-label mb-4">Detail Pemasukan</h3>
                        <div className="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kategori</th>
                                        <th style={{textAlign:'right'}}>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {pemasukan.map((item, idx) => (
                                        <tr key={idx}>
                                            <td>{item.tanggal}</td>
                                            <td><span className="badge badge-green">{item.kategori}</span></td>
                                            <td style={{textAlign:'right'}} className="mono tg">Rp {item.jumlah.toLocaleString('id-ID')}</td>
                                        </tr>
                                    ))}
                                    {pemasukan.length === 0 && <tr><td colSpan="3" className="tm" style={{textAlign:'center', padding:'20px'}}>Tidak ada data</td></tr>}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {/* Tabel Pengeluaran */}
                <div className="card">
                    <div className="card-body">
                        <h3 className="form-label mb-4">Detail Pengeluaran</h3>
                        <div className="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kategori</th>
                                        <th style={{textAlign:'right'}}>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {pengeluaran.map((item, idx) => (
                                        <tr key={idx}>
                                            <td>{item.tanggal}</td>
                                            <td><span className="badge badge-red">{item.kategori}</span></td>
                                            <td style={{textAlign:'right'}} className="mono tr">Rp {item.jumlah.toLocaleString('id-ID')}</td>
                                        </tr>
                                    ))}
                                    {pengeluaran.length === 0 && <tr><td colSpan="3" className="tm" style={{textAlign:'center', padding:'20px'}}>Tidak ada data</td></tr>}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
