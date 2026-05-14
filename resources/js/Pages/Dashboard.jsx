import React from 'react';
import AppLayout from '../Layouts/AppLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ totalMasuk, totalKeluar, saldo, label, periode, chart }) {
    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="page-header">
                <div>
                    <h1 className="page-title">Dashboard</h1>
                    <p className="page-sub">Periode: {label}</p>
                </div>
            </div>
            
            <div className="stat-grid">
                <div className="stat-card">
                    <h3 className="form-label" style={{color:'var(--green-600)'}}>Total Pemasukan</h3>
                    <p className="mono tg" style={{fontSize:'20px'}}>Rp {totalMasuk.toLocaleString('id-ID')}</p>
                </div>
                <div className="stat-card">
                    <h3 className="form-label" style={{color:'var(--red-600)'}}>Total Pengeluaran</h3>
                    <p className="mono tr" style={{fontSize:'20px'}}>Rp {totalKeluar.toLocaleString('id-ID')}</p>
                </div>
                <div className="stat-card">
                    <h3 className="form-label">Saldo Bersih</h3>
                    <p className="mono" style={{fontSize:'20px'}}>Rp {saldo.toLocaleString('id-ID')}</p>
                </div>
            </div>

            <div className="card">
                <div className="card-body">
                    <h3 className="form-label mb-4">Grafik 7 Hari Terakhir</h3>
                    <div className="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th style={{textAlign:'right'}}>Masuk</th>
                                    <th style={{textAlign:'right'}}>Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                {chart.map((day, idx) => (
                                    <tr key={idx}>
                                        <td>{day.tanggal}</td>
                                        <td style={{textAlign:'right'}} className="tg mono">Rp {day.pemasukan.toLocaleString('id-ID')}</td>
                                        <td style={{textAlign:'right'}} className="tr mono">Rp {day.pengeluaran.toLocaleString('id-ID')}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
