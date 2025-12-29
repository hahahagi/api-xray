<?php
include 'koneksi.php';
header('Content-Type: application/json');

$mode = $_GET['mode'] ?? '';

// =========================================================================
// MODE INPUT: Menerima Status HANYA ON/OFF dari Mesin
// =========================================================================
if ($mode == 'input_status') {

    // 1. Terima Data (Hanya Status!)
    $status_baru = $_GET['status']; // 'ON' atau 'OFF'
    $now = date("Y-m-d H:i:s");
    $today = date("Y-m-d");

    // 2. Ambil Data Log Terakhir untuk Hitung Durasi
    $sql_last = "SELECT * FROM tb_machine_log ORDER BY id DESC LIMIT 1";
    $res_last = $conn->query($sql_last);

    if ($res_last->num_rows > 0) {
        $last_data = $res_last->fetch_assoc();
        $last_time = strtotime($last_data['timestamp']);
        $current_time = strtotime($now);
        $diff_minutes = ($current_time - $last_time) / 60; // Selisih dalam menit

        // Jika status sebelumnya ON, berarti mesin baru saja bekerja selama X menit
        if ($last_data['status'] == 'ON') {

            // --- Update Tabel PERFORMANCE (Hijau Muda - Poin 5) ---
            // Asumsi: Kecepatan mesin = 10 ban per menit (Sesuaikan angka ini)
            $ban_added = round($diff_minutes * 10);

            // Cek apakah hari ini sudah ada record
            $check_perf = $conn->query("SELECT id FROM tb_performance WHERE shift_date='$today'");
            if ($check_perf->num_rows == 0) {
                $conn->query("INSERT INTO tb_performance (shift_date, total_ban_check) VALUES ('$today', $ban_added)");
            } else {
                $conn->query("UPDATE tb_performance SET total_ban_check = total_ban_check + $ban_added WHERE shift_date='$today'");
            }

            // --- Update Tabel QUALITY (Biru - Poin 6) ---
            // Asumsi: Rasio OK 95%, NG 5% (Karena tidak ada sensor NG)
            $ng_added = round($ban_added * 0.05);
            $ok_added = $ban_added - $ng_added;

            $check_qual = $conn->query("SELECT id FROM tb_quality WHERE shift_date='$today'");
            if ($check_qual->num_rows == 0) {
                $conn->query("INSERT INTO tb_quality (shift_date, jumlah_ok, jumlah_ng) VALUES ('$today', $ok_added, $ng_added)");
            } else {
                $conn->query("UPDATE tb_quality SET jumlah_ok = jumlah_ok + $ok_added, jumlah_ng = jumlah_ng + $ng_added WHERE shift_date='$today'");
            }
        }
    }

    // 3. Simpan Status Baru ke Tabel LOG (Hijau Tua - Poin 1,2,3,4)
    // Simpan hanya jika status berubah atau sebagai heartbeat per menit
    $conn->query("INSERT INTO tb_machine_log (status, timestamp) VALUES ('$status_baru', '$now')");

    // 4. Cek Tabel CONTROL (Oranye - Poin 7)
    $res_ctrl = $conn->query("SELECT target_status FROM tb_control WHERE id=1");
    $ctrl = $res_ctrl->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "saved_status" => $status_baru,
        "control_command" => $ctrl['target_status'] // Balasan untuk mematikan mesin
    ]);

    // =========================================================================
    // MODE LAPORAN: Menghitung Data untuk Ditampilkan di Web
    // =========================================================================
}

elseif ($mode == 'input_xray_checked') {

    $now = date("Y-m-d H:i:s");
    
    $result = $_GET['result']; // 'NG' atau 'OK'

    $conn->query("INSERT INTO tb_xray_log (result, timestamp) VALUES ('$result', '$now')");

    echo json_encode([
        "status" => "success",
        "saved_result" => $result
    ]);

}

elseif ($mode == 'kontrol_update') {
    $status = $_GET['status'];

    $conn->query("UPDATE tb_control SET target_status = '$status' WHERE id=1");

    if ($status) {
        echo json_encode([
            "message" => "OK",
            "mesin" => "1",
            "status" => $status
        ]);
    } else {
        echo json_encode(["message" => "Belum ada data"]);
    }
}

elseif ($mode == 'kontrol_baca') {
    
    $res_ctrl = $conn->query("SELECT id,target_status FROM tb_control WHERE id=1");
    $ctrl = $res_ctrl->fetch_assoc();

     echo json_encode([
        "message" => "success",
        "mesin" => $ctrl['id'],
        "status" => $ctrl['target_status']
    ]);
}

elseif ($mode == 'get_dashboard_data') {

    $today = date("Y-m-d");

    // A. Hitung Waktu Operasi & Downtime (Logika Kompleks dari Log)
    // Kita ambil semua log hari ini
    $sql_logs = "SELECT * FROM tb_machine_log WHERE DATE(timestamp) = '$today' ORDER BY id ASC";
    $logs = $conn->query($sql_logs);

    $runtime_minutes = 0;
    $downtime_minutes = 0;
    $last_ts = null;
    $last_st = null;
    $current_status = "OFF";

    while ($row = $logs->fetch_assoc()) {
        $curr_ts = strtotime($row['timestamp']);
        $current_status = $row['status']; // Status terkini

        if ($last_ts !== null) {
            $diff = ($curr_ts - $last_ts) / 60;
            if ($last_st == 'ON') {
                $runtime_minutes += $diff;
            } else {
                $downtime_minutes += $diff;
            }
        }
        $last_ts = $curr_ts;
        $last_st = $row['status'];
    }

    // B. Ambil Data Performance & Quality
    $perf = $conn->query("SELECT total_ban_check FROM tb_performance WHERE shift_date='$today'")->fetch_assoc();
    $qual = $conn->query("SELECT jumlah_ok, jumlah_ng FROM tb_quality WHERE shift_date='$today'")->fetch_assoc();
    $ctrl = $conn->query("SELECT target_status FROM tb_control WHERE id=1")->fetch_assoc();

    // OUTPUT JSON SESUAI 7 POIN EXCEL
    echo json_encode([
        // Tabel Hijau Tua
        "1_status_monitoring" => $current_status,
        "2_waktu_operasi"     => round($runtime_minutes, 1) . " Menit",
        "3_waktu_downtime"    => round($downtime_minutes, 1) . " Menit",
        "4_log_terakhir"      => $last_ts ? date("H:i:s", $last_ts) : "-",

        // Tabel Hijau Muda
        "5_total_ban_check"   => $perf['total_ban_check'] ?? 0,

        // Tabel Biru
        "6_quality_ok"        => $qual['jumlah_ok'] ?? 0,
        "6_quality_ng"        => $qual['jumlah_ng'] ?? 0,

        // Tabel Oranye
        "7_status_kontrol"    => $ctrl['target_status']
    ]);
} 
