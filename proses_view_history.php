<?php
session_start();
$server = "localhost";
$username = "root";
$password = "";
$dbname = "dealer";
$koneksi = mysqli_connect($server, $username, $password, $dbname);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $noRangkaView = $_POST['noRangkaView'];
    $noMutasiView = $_POST['noMutasiView']
    $tanggalMutasiView = $_POST['tanggalmutasiView'];
    $customerView = $_POST['customerView'];
    $picView = $_POST['picView'];
    $keteranganView = $_POST['keteranganView'];
    $postedDateView = $_POST['postedDateView'];

    // Simpan data ke database (sesuai kebutuhan Anda)
    $insert_query = "INSERT INTO mutasi (No_Rangka, No_Mutasi, Tanggal_Mutasi, Lokasi_Asal, Customer, PIC, Keterangan, PostedDate) 
                    VALUES ('$noRangkaView', '$noMutasiView', '$tanggalKirimView', '$lokasiAsalView', '$customerView', '$picView', '$keteranganView', '$postedDateView')";

    if (mysqli_query($koneksi, $insert_query)) {
        echo "Data berhasil direkam.";
    } else {
        echo "Error: " . $insert_query . "<br>" . mysqli_error($koneksi);
    }
} else {
    echo "Akses tidak sah.";
}

mysqli_close($koneksi);
?>
