<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>X-Ray Inspection Monitoring API</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 20px;
            color: #000;
        }

        h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .bold {
            font-weight: bold;
        }

        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }

        ul {
            list-style-type: disc;
            margin-left: 20px;
            line-height: 1.8;
        }

        a {
            text-decoration: underline;
            color: #4b0082;
            font-size: 18px;
        }
    </style>
</head>

<body>

    <h1>X-Ray Inspection Monitoring API</h1>

    <div>
        Status API: <span class="bold" style="color:green;">Online</span>
    </div>

    <hr>

    <h3>1. Simulasi Input Mesin (Hanya ON/OFF)</h3>
    <p>Karena mesin hanya mengirim status, klik ini berulang kali dengan jeda waktu untuk mensimulasikan durasi kerja.</p>
    <ul>
        <li>
            <a href="api.php?mode=input_status&status=ON" target="_blank">
                Kirim Signal: <strong>MESIN ON</strong> (Mesin Berjalan)
            </a>
            <br><small>Sistem akan mulai menghitung runtime & menambah jumlah ban otomatis.</small>
        </li>
        <li>
            <a href="api.php?mode=input_status&status=OFF" target="_blank">
                Kirim Signal: <strong>MESIN OFF</strong> (Mesin Berhenti)
            </a>
            <br><small>Sistem akan stop hitung runtime dan mulai hitung downtime.</small>
        </li>
    </ul>

    <hr>

    <h3>2. Cek Semua Metrik (Poin 1 - 7)</h3>
    <p>Data di bawah ini dihitung otomatis oleh PHP (Backend) berdasarkan log ON/OFF di atas.</p>
    <ul>
        <li>
            <a href="api.php?mode=get_dashboard_data" target="_blank">
                <strong>LIHAT JSON DASHBOARD LENGKAP</strong>
            </a>
            <br>
            Akan menampilkan:
            <ul>
                <li>Status Monitoring (Poin 1)</li>
                <li>Total Waktu Operasi (Poin 2)</li>
                <li>Total Waktu Downtime (Poin 3)</li>
                <li>Log Terakhir (Poin 4)</li>
                <li>Total Ban Check (Poin 5 - <i>Estimasi</i>)</li>
                <li>Quality OK/NG (Poin 6 - <i>Estimasi</i>)</li>
                <li>Status Kontrol (Poin 7)</li>
            </ul>
        </li>
    </ul>

    <hr>

    <h3>3. Panel Kontrol (Ubah Target Server)</h3>
    <p>Gunakan tombol ini untuk mengubah <b>control_command</b> yang akan diterima alat.</p>

    <!-- Logika PHP Kecil untuk Update Kontrol -->
    <?php
    if (isset($_GET['set_control'])) {
        $set = $_GET['set_control'];
        $conn->query("UPDATE tb_control SET target_status='$set' WHERE id=1");
        echo "<script>alert('Perintah diubah jadi $set'); window.location='index.php';</script>";
    }
    ?>

    <button onclick="window.location.href='?set_control=ON'" style="background:green; color:white; padding:10px; cursor:pointer;">
        SET PERINTAH: ON
    </button>

    <button onclick="window.location.href='?set_control=OFF'" style="background:red; color:white; padding:10px; cursor:pointer;">
        SET PERINTAH: OFF
    </button>

</body>

</html>