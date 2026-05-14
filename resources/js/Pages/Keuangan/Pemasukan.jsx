import React, { useState } from 'react';
import AppLayout from '../../Layouts/AppLayout';
import { useForm, router } from '@inertiajs/react';

export default function Pemasukan({ items, total, bulan, tahun }) {
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);

    const { data, setData, post, put, delete: destroy, reset, processing, errors } = useForm({
        tanggal: new Date().toISOString().split('T')[0],
        jumlah: '',
        kategori: 'penjualan',
        keterangan: '',
    });

    const openAddModal = () => {
        reset();
        setIsAddModalOpen(true);
    };

    const openEditModal = (item) => {
        setSelectedItem(item);
        setData({
            tanggal: item.tanggal,
            jumlah: item.jumlah,
            kategori: item.kategori,
            keterangan: item.keterangan || '',
        });
        setIsEditModalOpen(true);
    };

    const handleAdd = (e) => {
        e.preventDefault();
        post('/pemasukan', {
            onSuccess: () => {
                setIsAddModalOpen(false);
                reset();
            }
        });
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        put(`/pemasukan/${selectedItem.id}`, {
            onSuccess: () => setIsEditModalOpen(false)
        });
    };

    const handleDelete = (id) => {
        if (confirm('Hapus pemasukan ini?')) {
            destroy(`/pemasukan/${id}`);
        }
    };

    const handleFilterChange = (field, value) => {
        router.get('/pemasukan', { 
            ...router.page.props.ziggy.query, 
            [field]: value 
        }, { preserveState: true });
    };

    return (
        <AppLayout>
            <div className="page-header">
                <div>
                    <h1 className="page-title">Pemasukan</h1>
                    <p className="page-sub">{items.length} transaksi — Total: <strong className="tg">Rp {total.toLocaleString('id-ID')}</strong></p>
                </div>
                <button className="btn btn-primary" onClick={openAddModal}>
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pemasukan
                </button>
            </div>

            <div className="filter-bar">
                <label>Bulan:</label>
                <select className="fsel" value={bulan} onChange={(e) => handleFilterChange('bulan', e.target.value)}>
                    {['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'].map((n, i) => (
                        <option key={i + 1} value={i + 1}>{n}</option>
                    ))}
                </select>
                <label>Tahun:</label>
                <select className="fsel" value={tahun} onChange={(e) => handleFilterChange('tahun', e.target.value)}>
                    {[2023, 2024, 2025, 2026].map(y => (
                        <option key={y} value={y}>{y}</option>
                    ))}
                </select>
            </div>

            <div className="card">
                <div className="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                                <th style={{ textAlign: 'right' }}>Jumlah</th>
                                <th style={{ textAlign: 'right' }}>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.tanggal_label}</td>
                                    <td>
                                        <span className={`badge ${item.kategori === 'penjualan' ? 'badge-green' : (item.kategori === 'titip_jual' ? 'badge-amber' : 'badge-gray')}`}>
                                            {item.kategori_label}
                                        </span>
                                    </td>
                                    <td className="tm">{item.keterangan || '—'}</td>
                                    <td style={{ textAlign: 'right' }} className="mono tg">Rp {item.jumlah.toLocaleString('id-ID')}</td>
                                    <td>
                                        <div style={{ display: 'flex', gap: '6px', justifyContent: 'flex-end' }}>
                                            <button className="btn btn-secondary btn-sm" onClick={() => openEditModal(item)}>✏️</button>
                                            <button className="btn btn-danger btn-sm" onClick={() => handleDelete(item.id)}>🗑</button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {items.length === 0 && (
                                <tr>
                                    <td colSpan="5">
                                        <div className="empty">
                                            <div className="empty-ico">📋</div>
                                            <p className="empty-ttl">Belum ada data pemasukan</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Modal Tambah */}
            {isAddModalOpen && (
                <div className="modal-overlay open">
                    <div className="modal">
                        <div className="modal-header">
                            <h3 className="modal-title">Tambah Pemasukan</h3>
                            <button className="modal-close" onClick={() => setIsAddModalOpen(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleAdd}>
                                <div className="form-group">
                                    <label className="form-label">Tanggal *</label>
                                    <input type="date" className="form-control" value={data.tanggal} onChange={e => setData('tanggal', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Kategori *</label>
                                    <select className="form-control" value={data.kategori} onChange={e => setData('kategori', e.target.value)} required>
                                        <option value="penjualan">Penjualan Tempe</option>
                                        <option value="titip_jual">Titip Jual</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Jumlah (Rp) *</label>
                                    <input type="number" className="form-control" value={data.jumlah} onChange={e => setData('jumlah', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Keterangan</label>
                                    <input type="text" className="form-control" value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} />
                                </div>
                                <div style={{ display: 'flex', gap: '10px', justifyContent: 'flex-end', marginTop: '20px' }}>
                                    <button type="button" className="btn btn-secondary" onClick={() => setIsAddModalOpen(false)}>Batal</button>
                                    <button type="submit" className="btn btn-primary" disabled={processing}>Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* Modal Edit */}
            {isEditModalOpen && (
                <div className="modal-overlay open">
                    <div className="modal">
                        <div className="modal-header">
                            <h3 className="modal-title">Edit Pemasukan</h3>
                            <button className="modal-close" onClick={() => setIsEditModalOpen(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleUpdate}>
                                <div className="form-group">
                                    <label className="form-label">Tanggal *</label>
                                    <input type="date" className="form-control" value={data.tanggal} onChange={e => setData('tanggal', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Kategori *</label>
                                    <select className="form-control" value={data.kategori} onChange={e => setData('kategori', e.target.value)} required>
                                        <option value="penjualan">Penjualan Tempe</option>
                                        <option value="titip_jual">Titip Jual</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Jumlah (Rp) *</label>
                                    <input type="number" className="form-control" value={data.jumlah} onChange={e => setData('jumlah', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Keterangan</label>
                                    <input type="text" className="form-control" value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} />
                                </div>
                                <div style={{ display: 'flex', gap: '10px', justifyContent: 'flex-end', marginTop: '20px' }}>
                                    <button type="button" className="btn btn-secondary" onClick={() => setIsEditModalOpen(false)}>Batal</button>
                                    <button type="submit" className="btn btn-primary" disabled={processing}>Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
