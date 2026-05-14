import React, { useState } from 'react';
import AppLayout from '../../Layouts/AppLayout';
import { useForm } from '@inertiajs/react';

export default function Index({ items }) {
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isAdjustModalOpen, setIsAdjustModalOpen] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);

    const { data, setData, post, put, delete: destroy, reset, processing, errors } = useForm({
        nama_barang: '',
        jumlah_stok: 0,
        satuan: 'kg',
        jenis: 'masuk',
        jumlah: 0,
        keterangan: '',
    });

    const openAddModal = () => {
        reset();
        setIsAddModalOpen(true);
    };

    const openEditModal = (item) => {
        setSelectedItem(item);
        setData({
            nama_barang: item.nama_barang,
            satuan: item.satuan,
        });
        setIsEditModalOpen(true);
    };

    const openAdjustModal = (item) => {
        setSelectedItem(item);
        setData({
            jenis: 'masuk',
            jumlah: 0,
            keterangan: '',
        });
        setIsAdjustModalOpen(true);
    };

    const handleAdd = (e) => {
        e.preventDefault();
        post('/inventori', {
            onSuccess: () => {
                setIsAddModalOpen(false);
                reset();
            }
        });
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        put(`/inventori/${selectedItem.id}`, {
            onSuccess: () => setIsEditModalOpen(false)
        });
    };

    const handleAdjust = (e) => {
        e.preventDefault();
        post(`/inventori/${selectedItem.id}/adjust`, {
            onSuccess: () => {
                setIsAdjustModalOpen(false);
                reset();
            }
        });
    };

    const handleDelete = (id) => {
        if (confirm('Hapus barang ini dari inventori?')) {
            destroy(`/inventori/${id}`);
        }
    };

    return (
        <AppLayout>
            <div className="page-header">
                <div>
                    <h1 className="page-title">Inventori Stok</h1>
                    <p className="page-sub">{items.length} Barang Terdaftar</p>
                </div>
                <button className="btn btn-primary" onClick={openAddModal}>
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Barang
                </button>
            </div>

            <div className="card">
                <div className="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Update Terakhir</th>
                                <th style={{ textAlign: 'right' }}>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.map((item) => (
                                <tr key={item.id}>
                                    <td style={{ fontWeight: 600 }}>{item.nama_barang}</td>
                                    <td>
                                        <span className={`badge ${item.jumlah_stok > 0 ? 'badge-green' : 'badge-red'}`}>
                                            {item.jumlah_stok}
                                        </span>
                                    </td>
                                    <td>{item.satuan_label}</td>
                                    <td className="tm" style={{ fontSize: '13px' }}>{item.updated_at}</td>
                                    <td>
                                        <div style={{ display: 'flex', gap: '6px', justifyContent: 'flex-end' }}>
                                            <button className="btn btn-secondary btn-sm" onClick={() => openAdjustModal(item)} title="Sesuaikan Stok">⚖️</button>
                                            <button className="btn btn-secondary btn-sm" onClick={() => openEditModal(item)} title="Edit Nama/Satuan">✏️</button>
                                            <button className="btn btn-danger btn-sm" onClick={() => handleDelete(item.id)}>🗑</button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {items.length === 0 && (
                                <tr>
                                    <td colSpan="5">
                                        <div className="empty">
                                            <div className="empty-ico">📦</div>
                                            <p className="empty-ttl">Belum ada data inventori</p>
                                            <button className="btn btn-primary btn-sm" onClick={openAddModal}>+ Tambah Sekarang</button>
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
                            <h3 className="modal-title">Tambah Barang Baru</h3>
                            <button className="modal-close" onClick={() => setIsAddModalOpen(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleAdd}>
                                <div className="form-group">
                                    <label className="form-label">Nama Barang *</label>
                                    <input type="text" className="form-control" value={data.nama_barang} onChange={e => setData('nama_barang', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Stok Awal *</label>
                                    <input type="number" step="0.01" className="form-control" value={data.jumlah_stok} onChange={e => setData('jumlah_stok', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Satuan *</label>
                                    <select className="form-control" value={data.satuan} onChange={e => setData('satuan', e.target.value)} required>
                                        <option value="kg">Kilogram</option>
                                        <option value="ons">Ons</option>
                                        <option value="pcs">Pieces</option>
                                        <option value="pack">Pack</option>
                                        <option value="zak">Zak</option>
                                        <option value="liter">Liter</option>
                                        <option value="unit">Unit</option>
                                    </select>
                                </div>
                                <div style={{ display: 'flex', gap: '10px', justifyContent: 'flex-end', marginTop: '20px' }}>
                                    <button type="button" className="btn btn-secondary" onClick={() => setIsAddModalOpen(false)}>Batal</button>
                                    <button type="submit" className="btn btn-primary" disabled={processing}>Simpan</button>
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
                            <h3 className="modal-title">Edit Barang</h3>
                            <button className="modal-close" onClick={() => setIsEditModalOpen(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleUpdate}>
                                <div className="form-group">
                                    <label className="form-label">Nama Barang *</label>
                                    <input type="text" className="form-control" value={data.nama_barang} onChange={e => setData('nama_barang', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Satuan *</label>
                                    <select className="form-control" value={data.satuan} onChange={e => setData('satuan', e.target.value)} required>
                                        <option value="kg">Kilogram</option>
                                        <option value="ons">Ons</option>
                                        <option value="pcs">Pieces</option>
                                        <option value="pack">Pack</option>
                                        <option value="zak">Zak</option>
                                        <option value="liter">Liter</option>
                                        <option value="unit">Unit</option>
                                    </select>
                                </div>
                                <div style={{ display: 'flex', gap: '10px', justifyContent: 'flex-end', marginTop: '20px' }}>
                                    <button type="button" className="btn btn-secondary" onClick={() => setIsEditModalOpen(false)}>Batal</button>
                                    <button type="submit" className="btn btn-primary" disabled={processing}>Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* Modal Penyesuaian Stok */}
            {isAdjustModalOpen && (
                <div className="modal-overlay open">
                    <div className="modal">
                        <div className="modal-header">
                            <h3 className="modal-title">Sesuaikan Stok: {selectedItem?.nama_barang}</h3>
                            <button className="modal-close" onClick={() => setIsAdjustModalOpen(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleAdjust}>
                                <div className="form-group">
                                    <label className="form-label">Jenis Pergerakan *</label>
                                    <select className="form-control" value={data.jenis} onChange={e => setData('jenis', e.target.value)} required>
                                        <option value="masuk">Masuk (Penambahan Stok)</option>
                                        <option value="keluar">Keluar (Pengurangan Stok)</option>
                                    </select>
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Jumlah ({selectedItem?.satuan}) *</label>
                                    <input type="number" step="0.01" className="form-control" value={data.jumlah} onChange={e => setData('jumlah', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Keterangan</label>
                                    <input type="text" className="form-control" value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} placeholder="Contoh: Belanja bahan, Pemakaian produksi" />
                                </div>
                                <div style={{ display: 'flex', gap: '10px', justifyContent: 'flex-end', marginTop: '20px' }}>
                                    <button type="button" className="btn btn-secondary" onClick={() => setIsAdjustModalOpen(false)}>Batal</button>
                                    <button type="submit" className="btn btn-primary" disabled={processing}>Simpan Pergerakan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
