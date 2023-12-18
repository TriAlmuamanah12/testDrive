<?php
session_start();

// Pastikan metode yang digunakan adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $editNoRangka = $_POST["noRangkaEdit"];
    $editNoMutasi = $_POST["noMutasiEdit"];
    $editTanggalMutasi = $_POST["editTanggalMutasi"];
    $editCustomer = $_POST["editCustomer"];
    $editPIC = $_POST["editPIC"];
    $editKeterangan = $_POST["editKeterangan"];
    $editTanggalTerima = $_POST["editTanggalTerima"];

    // Lakukan validasi data jika diperlukan

    // Selanjutnya, lakukan pemrosesan penyimpanan data ke dalam tabel mutasi
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dealer";

    $koneksi = mysqli_connect($server, $username, $password, $dbname);

    if (!$koneksi) {
        die("Koneksi ke database gagal: " . mysqli_connect_error());
    }

    // Lakukan update data pada tabel mutasi dengan prepared statement
    $updateQuery = "UPDATE mutasi SET 
                    Tanggal_Mutasi = ?,
                    Customer = ?,
                    PIC = ?,
                    Keterangan = ?,
                    Tanggal_Terima = ?
                    WHERE No_Rangka = ? AND No_Mutasi = ?";

    $stmt = mysqli_prepare($koneksi, $updateQuery);

    // Bind parameter ke prepared statement
    mysqli_stmt_bind_param($stmt, "ssssssi", $editTanggalMutasi, $editCustomer, $editPIC, $editKeterangan, $editTanggalTerima, $editNoRangka, $editNoMutasi);

    // Eksekusi prepared statement
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        die("Query gagal: " . mysqli_error($koneksi));
    }

    // Tutup prepared statement
    mysqli_stmt_close($stmt);

    // Tutup koneksi database
    mysqli_close($koneksi);

    // Kirim respon berhasil ke klien
    echo "Data berhasil diubah dan direkam ke tabel mutasi!";
} else {
    // Jika metode bukan POST, kirim respon error
    echo "Metode yang tidak valid!";
}
?>
