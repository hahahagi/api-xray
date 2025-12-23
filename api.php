<?php
// =========================================================================
// KONFIGURASI & HEADER
// =========================================================================
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

require 'koneksi.php';

// Konfigurasi Simulasi
$MACHINE_SPEED_PER_MINUTE = 10; // Jumlah ban per menit
$NG_RATIO = 0.05;               // 5% barang NG

// Ambil parameter mode
$mode = $_GET['mode'] ?? '';

// =========================================================================
// MODE 1: INPUT STATUS (DARI MESIN KE SERVER)
// =========================================================================
if ($mode == 'input_status') {
    // Validasi input status
    $status_baru = isset($_GET['status']) ? strtoupper($_GET['status']) : '';

    if ($status_baru !== 'ON' && $status_baru !== 'OFF') {
        echo json_encode(["status" => "error", "message" => "Status harus ON atau OFF"]);
        exit;
    }

    $now = date("Y-m-d H:i:s");
    $today = date("Y-m-d");

    // 1. Cek Data Log Terakhir
    $sql_last = "SELECT status, timestamp FROM tb_machine_log ORDER BY id DESC LIMIT 1";
    $res_last = $conn->query($sql_last);

    if ($res_last && $res_last->num_rows > 0) {
        $last_data = $res_last->fetch_assoc();

        // Hitung durasi jika data sebelumnya ada
        $last_time = strtotime($last_data['timestamp']);
        $current_time = strtotime($now);
        $diff_minutes = ($current_time - $last_time) / 60;

        // LOGIKA BISNIS: Jika mesin sebelumnya ON, hitung produksi
        if ($last_data['status'] == 'ON' && $diff_minutes > 0) {

            // Hitung Jumlah Ban
            $ban_added = round($diff_minutes * $MACHINE_SPEED_PER_MINUTE);

            // Hitung Quality
            $ng_added = round($ban_added * $NG_RATIO);
            $ok_added = $ban_added - $ng_added;

            if ($ban_added > 0) {
                // A. Update Tabel PERFORMANCE
                // Cek apakah data hari ini sudah ada
                $stmt_check = $conn->prepare("SELECT id FROM tb_performance WHERE shift_date = ?");
                $stmt_check->bind_param("s", $today);
                $stmt_check->execute();
                $res_check = $stmt_check->get_result();

                if ($res_check->num_rows == 0) {
                    $stmt_ins = $conn->prepare("INSERT INTO tb_performance (shift_date, total_ban_check) VALUES (?, ?)");
                    $stmt_ins->bind_param("si", $today, $ban_added);
                    $stmt_ins->execute();
                } else {
                    $stmt_upd = $conn->prepare("UPDATE tb_performance SET total_ban_check = total_ban_check + ? WHERE shift_date = ?");
                    $stmt_upd->bind_param("is", $ban_added, $today);
                    $stmt_upd->execute();
                }

                // B. Update Tabel QUALITY
                $stmt_check_qual = $conn->prepare("SELECT id FROM tb_quality WHERE shift_date = ?");
                $stmt_check_qual->bind_param("s", $today);
                $stmt_check_qual->execute();
                $res_check_qual = $stmt_check_qual->get_result();

                if ($res_check_qual->num_rows == 0) {
                    $stmt_ins_qual = $conn->prepare("INSERT INTO tb_quality (shift_date, jumlah_ok, jumlah_ng) VALUES (?, ?, ?)");
                    $stmt_ins_qual->bind_param("sii", $today, $ok_added, $ng_added);
                    $stmt_ins_qual->execute();
                } else {
                    $stmt_upd_qual = $conn->prepare("UPDATE tb_quality SET jumlah_ok = jumlah_ok + ?, jumlah_ng = jumlah_ng + ? WHERE shift_date = ?");
                    $stmt_upd_qual->bind_param("iis", $ok_added, $ng_added, $today);
                    $stmt_upd_qual->execute();
                }
            }
        }
    }

    // 2. Simpan Status Baru ke Log (Heartbeat)
    $stmt_log = $conn->prepare("INSERT INTO tb_machine_log (status, timestamp) VALUES (?, ?)");
    $stmt_log->bind_param("ss", $status_baru, $now);
    $stmt_log->execute();

    // 3. Cek Perintah Kontrol (Feedback ke Mesin)
    $sql_ctrl = "SELECT target_status FROM tb_control WHERE id=1 LIMIT 1";
    $res_ctrl = $conn->query($sql_ctrl);
    $ctrl_data = $res_ctrl->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "saved_status" => $status_baru,
        "control_command" => $ctrl_data['target_status'] ?? 'OFF'
    ]);

    // =========================================================================
    // MODE 2: DASHBOARD (UNTUK TAMPILAN WEB)
    // =========================================================================
} elseif ($mode == 'get_dashboard_data') {

    $today = date("Y-m-d");

    // A. Ambil Log Hari Ini untuk Hitung Runtime
    $stmt_logs = $conn->prepare("SELECT status, timestamp FROM tb_machine_log WHERE DATE(timestamp) = ? ORDER BY id ASC");
    $stmt_logs->bind_param("s", $today);
    $stmt_logs->execute();
    $logs = $stmt_logs->get_result();

    $runtime_minutes = 0;
    $downtime_minutes = 0;
    $last_ts = null;
    $last_st = null;
    $current_status = "OFF";
    $last_log_time = "-";

    while ($row = $logs->fetch_assoc()) {
        $curr_ts = strtotime($row['timestamp']);
        $current_status = $row['status']; // Status paling update di loop terakhir

        if ($last_ts !== null) {
            $diff = ($curr_ts - $last_ts) / 60; // Selisih menit
            if ($last_st == 'ON') {
                $runtime_minutes += $diff;
            } else {
                $downtime_minutes += $diff;
            }
        }
        $last_ts = $curr_ts;
        $last_st = $row['status'];
    }

    // Format waktu terakhir log
    if ($last_ts) {
        $last_log_time = date("H:i:s", $last_ts);
    }

    // B. Ambil Data Performance
    $stmt_perf = $conn->prepare("SELECT total_ban_check FROM tb_performance WHERE shift_date = ?");
    $stmt_perf->bind_param("s", $today);
    $stmt_perf->execute();
    $perf = $stmt_perf->get_result()->fetch_assoc();

    // C. Ambil Data Quality
    $stmt_qual = $conn->prepare("SELECT jumlah_ok, jumlah_ng FROM tb_quality WHERE shift_date = ?");
    $stmt_qual->bind_param("s", $today);
    $stmt_qual->execute();
    $qual = $stmt_qual->get_result()->fetch_assoc();

    // D. Ambil Status Kontrol
    $res_ctrl = $conn->query("SELECT target_status FROM tb_control WHERE id=1");
    $ctrl = $res_ctrl->fetch_assoc();

    // OUTPUT JSON FINAL
    echo json_encode([
        // Tabel Hijau Tua (Monitoring)
        "1_status_monitoring" => $current_status,
        "2_waktu_operasi"     => round($runtime_minutes, 1) . " Menit",
        "3_waktu_downtime"    => round($downtime_minutes, 1) . " Menit",
        "4_log_terakhir"      => $last_log_time,

        // Tabel Hijau Muda (Performance)
        "5_total_ban_check"   => (int)($perf['total_ban_check'] ?? 0),

        // Tabel Biru (Quality)
        "6_quality_ok"        => (int)($qual['jumlah_ok'] ?? 0),
        "6_quality_ng"        => (int)($qual['jumlah_ng'] ?? 0),

        // Tabel Oranye (Control)
        "7_status_kontrol"    => $ctrl['target_status'] ?? 'OFF'
    ]);
} else {
    // Mode tidak dikenali
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid mode"]);
}

// Tutup koneksi (Opsional tapi baik)
$conn->close();
