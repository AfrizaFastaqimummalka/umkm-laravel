-- ─────────────────────────────────────────────────────
-- UMKM Tempe — Setup Database NeonDB
-- Jalankan di NeonDB SQL Editor jika php artisan migrate gagal
-- ─────────────────────────────────────────────────────

DROP TABLE IF EXISTS inventori_movements CASCADE;
DROP TABLE IF EXISTS inventori CASCADE;
DROP TABLE IF EXISTS telegram_subscribers CASCADE;
DROP TABLE IF EXISTS pemasok CASCADE;
DROP TABLE IF EXISTS pelanggan CASCADE;
DROP TABLE IF EXISTS pengeluaran CASCADE;
DROP TABLE IF EXISTS pemasukan CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS cache CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS migrations CASCADE;

CREATE TABLE migrations (
    id SERIAL PRIMARY KEY, migration VARCHAR(255) NOT NULL, batch INTEGER NOT NULL
);

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL, password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE UNIQUE INDEX users_email_unique ON users(email);
CREATE UNIQUE INDEX users_username_unique ON users(username);

CREATE TABLE pemasukan (
    id BIGSERIAL PRIMARY KEY, tanggal DATE NOT NULL, jumlah DECIMAL(15,0) NOT NULL,
    keterangan VARCHAR(255) NULL, kategori VARCHAR(20) NOT NULL DEFAULT 'penjualan',
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE pengeluaran (
    id BIGSERIAL PRIMARY KEY, tanggal DATE NOT NULL, jumlah DECIMAL(15,0) NOT NULL,
    keterangan VARCHAR(255) NULL, kategori VARCHAR(20) NOT NULL DEFAULT 'operasional',
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE pelanggan (
    id BIGSERIAL PRIMARY KEY, nama VARCHAR(100) NOT NULL, no_hp VARCHAR(20) NULL,
    alamat TEXT NULL, catatan TEXT NULL,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE pemasok (
    id BIGSERIAL PRIMARY KEY, nama VARCHAR(100) NOT NULL, kontak VARCHAR(100) NULL,
    no_hp VARCHAR(20) NULL, alamat TEXT NULL, catatan TEXT NULL,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE inventori (
    id BIGSERIAL PRIMARY KEY, nama_barang VARCHAR(200) NOT NULL,
    jumlah_stok DECIMAL(15,2) NOT NULL DEFAULT 0, satuan VARCHAR(20) NOT NULL DEFAULT 'kg',
    tanggal_update TIMESTAMP NULL,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE inventori_movements (
    id BIGSERIAL PRIMARY KEY, inventori_id BIGINT NOT NULL REFERENCES inventori(id) ON DELETE CASCADE,
    jenis VARCHAR(10) NOT NULL CHECK(jenis IN ('masuk','keluar')),
    jumlah DECIMAL(15,2) NOT NULL, keterangan VARCHAR(255) NULL,
    tanggal TIMESTAMP NULL, user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);
CREATE TABLE telegram_subscribers (
    id BIGSERIAL PRIMARY KEY, chat_id VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(255) NULL, first_name VARCHAR(255) NULL, last_name VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT false, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
);

INSERT INTO migrations (migration, batch) VALUES
('2025_01_01_000001_create_users_table', 1),
('2025_01_01_000002_create_keuangan_tables', 1);
