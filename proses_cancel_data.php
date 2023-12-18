<?php
// proses_cancel_data.php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$dbname = "dealer";
$koneksi = mysqli_connect($server, $username, $password, $dbname);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Ambil data-no-mutasi dari permintaan POST
$noMutasi = isset($_POST['noMutasi']) ? (int)$_POST['noMutasi'] : 0;

// Lakukan pemrosesan pembatalan di sini
$tanggalBatal = date('Y-m-d H:i:s'); // Ambil tanggal dan waktu saat ini
$query = "UPDATE mutasi SET tanggal_batal = '$tanggalBatal' WHERE No_Mutasi = $noMutasi";

$result = mysqli_query($koneksi, $query);

if ($result) {
    echo json_encode(['status' => 'success', 'tanggal_batal' => $tanggalBatal]);
} else {
    $error_message = mysqli_error($koneksi);
    echo json_encode(['status' => 'error', 'message' => 'Gagal membatalkan data. ' . $error_message]);
}

mysqli_close($koneksi);
?>
