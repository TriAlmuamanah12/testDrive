<?php
// proses_post_data.php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$dbname = "dealer";
$koneksi = mysqli_connect($server, $username, $password, $dbname);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Ambil data dari permintaan POST
$noRangka = isset($_POST['noRangka']) ? $_POST['noRangka'] : '';
$postedDate = isset($_POST['postedDate']) ? $_POST['postedDate'] : '';

// Periksa apakah data telah dibatalkan sebelumnya
$queryCheckCanceled = "SELECT tanggal_batal FROM mutasi WHERE No_Rangka = '$noRangka'";
$resultCheckCanceled = mysqli_query($koneksi, $queryCheckCanceled);

if (!$resultCheckCanceled) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memeriksa status pembatalan data.']);
    exit;
}

$dataCanceled = mysqli_fetch_assoc($resultCheckCanceled);

if ($dataCanceled['Canceled'] == 1) {
    echo json_encode(['status' => 'error', 'message' => 'Data telah dibatalkan dan tidak dapat diposting.']);
    exit;
}

// Lanjutkan dengan pemrosesan posting data
$queryPostData = "UPDATE mutasi SET PostedDate = '$postedDate' WHERE No_Rangka = '$noRangka'";
$resultPostData = mysqli_query($koneksi, $queryPostData);

if ($resultPostData) {
    echo json_encode(['status' => 'success']);
} else {
    $error_message = mysqli_error($koneksi);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memposting data. ' . $error_message]);
}

mysqli_close($koneksi);
?>
