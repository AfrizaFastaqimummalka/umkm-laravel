import React, { useState } from 'react';
import AppLayout from '../../Layouts/AppLayout';
import { useForm, router } from '@inertiajs/react';

export default function Index({ items, search }) {
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null);

    const { data, setData, post, put, delete: destroy, reset, processing, errors } = useForm({
        nama: '',
        kontak: '',
        no_hp: '',
        alamat: '',
        catatan: '',
    });

    const openAddModal = () => {
        reset();
        setIsAddModalOpen(true);
    };

    const openEditModal = (item) => {
        setSelectedItem(item);
        setData({
            nama: item.nama,
            kontak: item.kontak || '',
            no_hp: item.no_hp || '',
            alamat: item.alamat || '',
            catatan: item.catatan || '',
        });
        setIsEditModalOpen(true);
    };

    const handleAdd = (e) => {
        e.preventDefault();
        post('/pemasok', {
            onSuccess: () => {
                setIsAddModalOpen(false);
                reset();
            }
        });
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        put(`/pemasok/${selectedItem.id}`, {
            onSuccess: () => setIsEditModalOpen(false)
        });
    };

    const handleDelete = (id) => {
        if (confirm('Hapus pemasok ini?')) {
            destroy(`/pemasok/${id}`);
        }
    };

    const handleSearch = (e) => {
        router.get('/pemasok', { search: e.target.value }, { preserveState: true });
    };

    return (
        <AppLayout>
            <div className="page-header">
                <div>
                    <h1 className="page-title">Pemasok (Supplier)</h1>
                    <p className="page-sub">{items.length} Pemasok Terdaftar</p>
                </div>
                <button className="btn btn-primary" onClick={openAddModal}>
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pemasok
                </button>
            </div>

            <div className="filter-bar">
                <input 
                    type="text" 
                    className="form-control" 
                    placeholder="Cari nama pemasok..." 
                    defaultValue={search}
                    onChange={handleSearch}
                    style={{ maxWidth: '300px' }}
                />
            </div>

            <div className="card">
                <div className="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Pemasok</th>
                                <th>Kontak Person</th>
                                <th>No. HP</th>
                                <th>Alamat</th>
                                <th style={{ textAlign: 'right' }}>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.map((item) => (
                                <tr key={item.id}>
                                    <td style={{ fontWeight: 600 }}>{item.nama}</td>
                                    <td>{item.kontak || '—'}</td>
                                    <td>{item.no_hp || '—'}</td>
                                    <td className="tm">{item.alamat || '—'}</td>
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
                                            <div className="empty-ico">🚚</div>
                                            <p className="empty-ttl">Belum ada data pemasok</p>
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
                            <h3 className="modal-title">Tambah Pemasok</h3>
                            <button className="modal-close" onClick={() => setIsAddModalOpen(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleAdd}>
                                <div className="form-group">
                                    <label className="form-label">Nama Pemasok *</label>
                                    <input type="text" className="form-control" value={data.nama} onChange={e => setData('nama', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Kontak Person</label>
                                    <input type="text" className="form-control" value={data.kontak} onChange={e => setData('kontak', e.target.value)} placeholder="Nama PIC" />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">No. HP</label>
                                    <input type="text" className="form-control" value={data.no_hp} onChange={e => setData('no_hp', e.target.value)} />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Alamat</label>
                                    <textarea className="form-control" value={data.alamat} onChange={e => setData('alamat', e.target.value)} rows="2"></textarea>
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Catatan</label>
                                    <input type="text" className="form-control" value={data.catatan} onChange={e => setData('catatan', e.target.value)} />
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
                            <h3 className="modal-title">Edit Pemasok</h3>
                            <button className="modal-close" onClick={() => setIsEditModalOpen(false)}>✕</button>
                        </div>
                        <div className="modal-body">
                            <form onSubmit={handleUpdate}>
                                <div className="form-group">
                                    <label className="form-label">Nama Pemasok *</label>
                                    <input type="text" className="form-control" value={data.nama} onChange={e => setData('nama', e.target.value)} required />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Kontak Person</label>
                                    <input type="text" className="form-control" value={data.kontak} onChange={e => setData('kontak', e.target.value)} />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">No. HP</label>
                                    <input type="text" className="form-control" value={data.no_hp} onChange={e => setData('no_hp', e.target.value)} />
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Alamat</label>
                                    <textarea className="form-control" value={data.alamat} onChange={e => setData('alamat', e.target.value)} rows="2"></textarea>
                                </div>
                                <div className="form-group">
                                    <label className="form-label">Catatan</label>
                                    <input type="text" className="form-control" value={data.catatan} onChange={e => setData('catatan', e.target.value)} />
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
        </AppLayout>
    );
}
