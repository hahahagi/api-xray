DROP DATABASE IF EXISTS db_xray_project;
CREATE DATABASE db_xray_project;
USE db_xray_project;

-- TABEL 1 (HIJAU TUA): Log Status Mesin (Untuk Poin 1, 2, 3, 4)
-- Mencatat setiap kali ada perubahan status atau "heartbeat" dari mesin
CREATE TABLE tb_machine_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(5), -- 'ON' atau 'OFF'
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABEL 2 (HIJAU MUDA): Performance (Untuk Poin 5)
-- Menyimpan kalkulasi jumlah ban berdasarkan durasi mesin ON
CREATE TABLE tb_performance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shift_date DATE,
    total_ban_check INT DEFAULT 0
);

-- TABEL 3 (BIRU): Quality (Untuk Poin 6)
-- Menyimpan data OK/NG (bisa diisi manual atau rasio otomatis)
CREATE TABLE tb_quality (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shift_date DATE,
    jumlah_ok INT DEFAULT 0,
    jumlah_ng INT DEFAULT 0
);

-- TABEL 4 (ORANYE): Kontrol (Untuk Poin 7)
CREATE TABLE tb_control (
    id INT PRIMARY KEY,
    target_status VARCHAR(5) DEFAULT 'ON'
);
INSERT INTO tb_control (id, target_status) VALUES (1, 'ON');