import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function AppLayout({ children }) {
    const { auth, flash } = usePage().props;
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);

    const toggleDrawer = () => setIsDrawerOpen(!isDrawerOpen);

    return (
        <div className="wrap">
            {/* Sidebar Desktop */}
            <aside className="sidebar">
                <SidebarContent />
            </aside>

            {/* Mobile Drawer */}
            {isDrawerOpen && (
                <div className="drawer-overlay" style={{ display: 'block' }} onClick={toggleDrawer}></div>
            )}
            <div className={`drawer ${isDrawerOpen ? 'open' : ''}`}>
                <SidebarContent />
            </div>

            <main className="main">
                <div className="topbar">
                    <button className="menu-btn" onClick={toggleDrawer}>
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span className="topbar-brand">🫘 UMKM Tempe</span>
                </div>

                <div className="page">
                    {flash?.success && <div className="alert alert-success">✅ {flash.success}</div>}
                    {flash?.error && <div className="alert alert-error">❌ {flash.error}</div>}
                    {children}
                </div>
            </main>
        </div>
    );
}

function SidebarContent() {
    const { url, props } = usePage();
    const { auth } = props;
    
    return (
        <>
            <div className="sb-brand">
                <div className="sb-icon">🫘</div>
                <div>
                    <div className="sb-name">UMKM Tempe</div>
                    <div className="sb-sub">{auth?.user?.name || 'Guest'}</div>
                </div>
            </div>
            <div className="sb-div"></div>
            <nav className="sb-nav">
                <div className="sb-lbl">Menu Utama</div>
                <Link href={route('dashboard')} className={`nav-item ${url === '/dashboard' ? 'active' : ''}`}>
                    <span>📊 Dashboard</span>
                </Link>
                <Link href={route('inventori.index')} className={`nav-item ${url.startsWith('/inventori') ? 'active' : ''}`}>
                    <span>📦 Inventori</span>
                </Link>
                <Link href={route('pemasukan.index')} className={`nav-item ${url.startsWith('/pemasukan') ? 'active' : ''}`}>
                    <span>💰 Pemasukan</span>
                </Link>
                <Link href={route('pengeluaran.index')} className={`nav-item ${url.startsWith('/pengeluaran') ? 'active' : ''}`}>
                    <span>💸 Pengeluaran</span>
                </Link>
                <Link href={route('pelanggan.index')} className={`nav-item ${url.startsWith('/pelanggan') ? 'active' : ''}`}>
                    <span>👥 Pelanggan</span>
                </Link>
                <Link href={route('pemasok.index')} className={`nav-item ${url.startsWith('/pemasok') ? 'active' : ''}`}>
                    <span>🚚 Pemasok</span>
                </Link>
                <Link href={route('laporan.index')} className={`nav-item ${url.startsWith('/laporan') ? 'active' : ''}`}>
                    <span>📋 Laporan</span>
                </Link>
                <Link href={route('settings.index')} className={`nav-item ${url.startsWith('/settings') ? 'active' : ''}`}>
                    <span>⚙️ Pengaturan</span>
                </Link>
                
                <div className="sb-lbl" style={{marginTop:'20px'}}>Sistem</div>
                <Link href={route('profile.edit')} className={`nav-item ${url.startsWith('/profile') ? 'active' : ''}`}>
                    <span>👤 Profil</span>
                </Link>
                <Link href={route('logout')} method="post" as="button" className="btn-out">
                    <span>🚪 Keluar</span>
                </Link>
            </nav>
        </>
    );
}
