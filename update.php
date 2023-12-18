<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noRangka = $_POST['No_Rangka'];
    $customer = $_POST['Customer'];
    $tanggalMutasi = $_POST['Tanggal_Mutasi'];
    $pic = $_POST['PIC'];
    $keterangan = $_POST['Keterangan'];

    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dealer";

    function connectToDatabase() {
        $koneksi = new mysqli($GLOBALS['server'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['dbname']);
        if ($koneksi->connect_error) {
            die("Koneksi ke database gagal: " . $koneksi->connect_error);
        }
        return $koneksi;
    }

    $createdBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'default_user';

    try {
        $koneksi = connectToDatabase();
        if (!$koneksi) {
            throw new Exception("Koneksi ke database gagal: " . mysqli_connect_error());
        }

        // Validasi Tanggal
        if (!strtotime($tanggalMutasi)) {
            throw new Exception('Gagal: Format tanggal tidak valid.');
        }

        $currentDateTime = date('Y-m-d H:i:s');
        $createdDate = $currentDateTime . '.' . substr((string) microtime(true), 2, 6);

        // Pengecekan data mutasi yang belum diposting
        $queryCheckPendingMutasi = "SELECT COUNT(*) as jumlahPending FROM mutasi WHERE No_Rangka = ? AND modifiedBy IS NULL";
        $stmtCheckPendingMutasi = $koneksi->prepare($queryCheckPendingMutasi);
        $stmtCheckPendingMutasi->bind_param("s", $noRangka);
        $stmtCheckPendingMutasi->execute();
        $resultCheckPendingMutasi = $stmtCheckPendingMutasi->get_result();

        if ($resultCheckPendingMutasi && $resultCheckPendingMutasi->num_rows > 0) {
            $dataPendingMutasi = $resultCheckPendingMutasi->fetch_assoc();
            $jumlahPending = $dataPendingMutasi['jumlahPending'];

            if ($jumlahPending > 0) {
                // Jangan tambahkan data baru jika sudah ada mutasi yang belum diposting
                echo 'Gagal: Sudah ada mutasi yang belum diposting untuk No_Rangka ini.';
                exit;
            }
        }

        // Ambil Lokasi_Asal dari master_unit
        $queryGetLokasiAsal = "SELECT Lokasi FROM master_unit WHERE No_Rangka = ?";
        $stmtGetLokasiAsal = $koneksi->prepare($queryGetLokasiAsal);
        $stmtGetLokasiAsal->bind_param("s", $noRangka);
        $stmtGetLokasiAsal->execute();
        $resultLokasiAsal = $stmtGetLokasiAsal->get_result();
        $lokasiAsal = '';

        if ($resultLokasiAsal && $resultLokasiAsal->num_rows > 0) {
            $dataLokasiAsal = $resultLokasiAsal->fetch_assoc();
            $lokasiAsal = $dataLokasiAsal['Lokasi'];
        }

        $koneksi->autocommit(FALSE);

        // Perubahan pada query insert mutasi
        $queryInsertMutasi = "INSERT INTO mutasi (No_Rangka, Lokasi_Asal, Customer, Tanggal_Mutasi, PIC, Keterangan, createdBy, modifiedBy, createdDate) VALUES (?, ?, ?, ?, ?, ?, ?, '', ?)";
        $stmtInsertMutasi = $koneksi->prepare($queryInsertMutasi);
        $stmtInsertMutasi->bind_param("ssssssss", $noRangka, $lokasiAsal, $customer, $tanggalMutasi, $pic, $keterangan, $createdBy, $createdDate);

        // Perubahan pada query update master_unit
        $stmtUpdateMaster_Unit = $koneksi->prepare("UPDATE master_unit SET Lokasi = ? WHERE No_Rangka = ? AND (SELECT COUNT(*) FROM mutasi WHERE No_Rangka = ? AND modifiedBy IS NULL) = 0");
        $stmtUpdateMaster_Unit->bind_param("sss", $lokasiAsal, $noRangka, $noRangka);

        if ($stmtInsertMutasi->execute() && $stmtUpdateMaster_Unit->execute()) {
            $koneksi->commit();
            echo 'success';
        } else {
            throw new Exception('Gagal: Tidak dapat menyimpan mutasi. Silakan cek data input. Error: ' . $koneksi->error . ', ' . $stmtInsertMutasi->error . ', ' . $stmtUpdateMaster_Unit->error);
        }
    } catch (Exception $e) {
        $koneksi->rollback();
        echo 'Gagal: ' . $e->getMessage();
    } finally {
        if (isset($koneksi)) {
            $koneksi->autocommit(TRUE);
            $koneksi->close();
        }
    }
} else {
    echo 'Metode yang diterima tidak valid.';
}
?>
