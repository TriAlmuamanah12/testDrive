<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .form-group {
            max-width: 400px;
            display: inline-block;
            margin-right: 20px;
            vertical-align: top;
        }

        .form-group:last-child {
            margin-right: 0;
        }

        .form-group,
        table {
            margin-bottom: 25px;
        }

        .table-buttons {
            display: flex;
            justify-content: space-between;
        }

        .table-buttons button {
            margin-right: 5px;
        }
    </style>
    </head>
<body>
<div class="container">
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
    if (isset($_GET['record'])) {
        $noRangka = (int)$_GET['record'];
        $master_unit_query = "SELECT * FROM master_unit WHERE No_Rangka = $noRangka";
        $master_unit_result = mysqli_query($koneksi, $master_unit_query);

        if (!$master_unit_result) {
            die("Query gagal: " . mysqli_error($koneksi));
        }
        ?>
        <form>
            <?php
            while ($master_unit_data = mysqli_fetch_assoc($master_unit_result)) {
                echo "<div class='form-group'>";
                echo "<label>Tipe</label>";
                echo "<input type='text' class='form-control' value='" . htmlspecialchars($master_unit_data['Tipe']) . "' readonly>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label>Warna</label>";
                echo "<input type='text' class='form-control' value='" . htmlspecialchars($master_unit_data['Warna']) . "' readonly>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label>No Rangka</label>";
                echo "<input type='text' class='form-control' value='" . htmlspecialchars($master_unit_data['No_Rangka']) . "' readonly>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label>No Mesin</label>";
                echo "<input type='text' class='form-control' value='" . htmlspecialchars($master_unit_data['No_Mesin']) . "' readonly>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label>Tahun</label>";
                echo "<input type='text' class='form-control' value='" . htmlspecialchars($master_unit_data['Tahun']) . "' readonly>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label>Lokasi</label>";
                echo "<input type='text' class='form-control' value='" . htmlspecialchars($master_unit_data['Lokasi']) . "' readonly>";
                echo "</div>";

                echo "<div class='form-group'>";
                echo "<label>Tgl Register</label>";
                // Ubah format tanggal menggunakan date()
                $tanggalRegister = date('d-m-Y', strtotime($master_unit_data['Tanggal_Register']));
                echo "<input type='text' class='form-control' value='" . htmlspecialchars($tanggalRegister) . "' readonly>";
                echo "</div>";
                
                echo "<div class='form-group'>";
                echo "<label>Keterangan</label>";
                echo "<input type='text' class='form-control' value='" . htmlspecialchars($master_unit_data['Keterangan']) . "' readonly>";
                echo "</div>";
                
            }
            ?>
        </form>
        <?php
        } else {
            echo "Tidak ada nomor record yang dipilih.";
        }

        $query = "SELECT No_Rangka, No_Mutasi, Tanggal_Mutasi, Lokasi_Asal, Customer, PIC, Keterangan, PostedDate, tanggal_batal FROM mutasi WHERE No_Rangka = $noRangka AND (tanggal_batal IS NULL OR tanggal_batal = '')";
        $result = mysqli_query($koneksi, $query);

        if (!$result) {
            die("Query gagal: " . mysqli_error($koneksi));
        }
        if (mysqli_num_rows($result) > 0) {
            ?>
            <table class="table table-bordered table-striped table-sm">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center">No TestDrive</th>
                    <th class="text-center">Tgl TestDrive</th>
                    <th>Customer</th>
                    <th>PIC Dealer</th>
                    <th>Keterangan</th>
                    <th class="text-center">Tgl Kembali</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
                <tbody>
                    <?php
                    while ($data_row = mysqli_fetch_assoc($result)) {
                        echo "<tr data-toggle='modal' data-target='#editHistoryModal'>";
                         echo "<td class='text-center'>TDR-" . date('ym-', strtotime($data_row['Tanggal_Mutasi'])) . sprintf("%03d", htmlspecialchars($data_row['No_Mutasi'])) . "</td>";
                        echo "<td class='text-center'>" . date('d-m-Y', strtotime(htmlspecialchars($data_row['Tanggal_Mutasi']))) . "</td>";
                        echo "<td>" . htmlspecialchars($data_row['Customer']) . "</td>";
                        echo "<td>" . htmlspecialchars($data_row['PIC']) . "</td>";
                        echo "<td>" . htmlspecialchars($data_row['Keterangan']) . "</td>";
                        echo "<td class='text-center'>";
                        if (!empty($data_row['PostedDate'])) {
                            echo date('d-m-Y', strtotime(htmlspecialchars($data_row['PostedDate'])));
                        }
                        echo "</td>";

                        echo "<td class='table-buttons text-center d-flex justify-content-center'>";
                        if ($data_row['PostedDate'] != null || $data_row['tanggal_batal']) {
                            // Data sudah diposting atau dibatalkan, tombol view
                            echo "<button class='btn btn-primary btn-sm view-btn' data-toggle='modal' data-target='#viewHistoryModal' data-no-rangka='" . htmlspecialchars($noRangka) . "'>View</button>";
                        } else {
                            // Data belum diposting atau dibatalkan, tombol edit dan cancel
                            echo "<button class='btn btn-warning btn-sm edit-btn' data-toggle='modal' data-target='#editHistoryModal' data-no-mutasi='" . htmlspecialchars($data_row['No_Mutasi']) . "'>Edit</button>";
                            echo "<button class='btn btn-danger btn-sm cancel-btn' data-no-mutasi='" . htmlspecialchars($data_row['No_Mutasi']) . "' data-tanggalbatal='" . htmlspecialchars($data_row['tanggal_batal']) . "' data-toggle='modal' data-target='#editCancelConfirmationModal'>Cancel</button>";
                        }
                        echo "</td>";
                        echo "</tr>";                        

                    }
                    ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
                <div class="text-center mb-4">
                    <button class="btn btn-primary" onclick="history.go(-1);">Kembali</button>
                </div>
            </div>
            <div class="modal fade" id="editHistoryModal" tabindex="-1" role="dialog" aria-labelledby="editHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editHistoryModalLabel">Edit History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="proses_edit_history.php" id="formEditHistory">
                    <input type="hidden" name="noRangkaEdit" id="noRangkaEdit" value="">
                    <input type="hidden" name="noMutasiEdit" id="noMutasiEdit" value="">

                    <div class="form-group">
                        <label for="editNoMutasi">No TestDrive</label>
                        <input type="text" name="editNoMutasi" id="editNoMutasi" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="editTanggalMutasi">Tanggal TestDrive</label>
                        <input type="date" name="editTanggalMutasi" id="editTanggalMutasi" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="editCustomer">Customer</label>
                        <input type="text" name="editCustomer" id="editCustomer" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="editPIC">PIC Dealer</label>
                        <input type="text" name="editPIC" id="editPIC" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="editKeterangan">Keterangan</label>
                        <textarea name="editKeterangan" id="editKeterangan" class="form-control" readonly></textarea>
                    </div>

                   <div class="form-group">
                    <label for="editTanggalTerima">Tanggal kembali<span style="color: red;">*</span></label>
                    <input type="date" name="editTanggalTerima" id="editTanggalTerima" class="form-control" required>
                </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="saveChanges">Posting</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>

                         <!-- Modal View History -->
                            <div class="modal fade" id="viewHistoryModal" tabindex="-1" role="dialog" aria-labelledby="viewHistoryModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewHistoryModalLabel">View History</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Form View History -->
                                            <form method="post" action="proses_view_history.php" id="formViewHistory">
                                                <input type="hidden" name="noRangkaView" id="noRangkaView" value="">
                                                <div class="form-group">
                                                <label>No TestDrive</label>
                                                <input type="text" name="noMutasiView" id="noMutasiView" class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal TestDrive</label>
                                                <input type="text" name="tanggalMutasiView" id="tanggalMutasiView" class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <input type="text" name="CustomerView" id="CustomerView" class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>PIC Dealer</label>
                                                <input type="text" name="picView" id="picView" class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Keterangan</label>
                                                <textarea name="keteranganView" id="keteranganView" class="form-control" readonly></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Kembali</label>
                                                <input type="text" name="postedDateView" id="postedDateView" class="form-control" readonly>
                                            </div>
                                            <!-- Tombol untuk menutup modal -->
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                        $(document).ready(function () {
                            // Event handler untuk menampilkan modal editHistoryModal
                            $('#editHistoryModal').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget);
                                var noRangka = button.data('no-rangka');
                                var noMutasi = button.data('no-mutasi');
                                var tanggalMutasi = $('#editTanggalMutasi').val();
                                var tanggalMutasi = button.closest('tr').find('td:eq()').text();

                                // Mengatur nilai pada formulir untuk pengeditan
                                $('#noRangkaEdit').val(noRangka);
                                $('#noMutasiEdit').val(noMutasi);
                                $('#editNoMutasi').val(noMutasi);

                                if (status === 'canceled') {
                                    // Jika data telah dibatalkan, tambahkan kelas Bootstrap 'disabled' pada tombol
                                    $('#editHistoryModal .edit-btn, #editHistoryModal .cancel-btn').addClass('disabled');
                                    // Nonaktifkan formulir jika data telah dibatalkan
                                    $('#formEditHistory :input').prop('disabled', true);
                                } else {
                                    // Jika data belum dibatalkan, hapus kelas Bootstrap 'disabled' pada tombol
                                    $('#editHistoryModal .edit-btn, #editHistoryModal .cancel-btn').removeClass('disabled');
                                    // Aktifkan formulir jika data belum dibatalkan
                                    $('#formEditHistory :input').prop('disabled', false);
                                }
                                
                                // Mendapatkan tanggal saat ini untuk baris yang diedit
                                var currentDate = new Date(); // Anda mungkin perlu menyesuaikan ini berdasarkan format tanggal Anda
                                var formattedDate = currentDate.getFullYear() + '-' + ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + ('0' + currentDate.getDate()).slice(-2);

                                // Menetapkan tanggal yang diedit pada formulir
                                $('#editTanggalMutasi').val(formattedDate);

                                // ... (kolom formulir lainnya)

                                $('#editNoMutasi').val(button.closest('tr').find('td:eq(0)').text());
                                $('#esittanggalMutasi').val(button.closest('tr').find('td:eq(1)').text());
                                $('#editCustomer').val(button.closest('tr').find('td:eq(2)').text());
                                $('#editPIC').val(button.closest('tr').find('td:eq(3)').text());
                                $('#editKeterangan').val(button.closest('tr').find('td:eq(4)').text());
                                $('#editTanggalTerima').val(button.closest('tr').find('td:eq(5)').text());
                            });

                            // Event handler untuk pengiriman formulirEditHistory
                            $('#formEditHistory').submit(function (event) {
                                event.preventDefault();

                                // Permintaan Ajax untuk mengirim data formulir
                                $.ajax({
                                    type: 'POST',
                                    url: 'update_posted_date.php',
                                    data: $(this).serialize(),
                                    success: function (response) {
                                        console.log("ini respon : " + response);
                                        alert('Data Berhasil di Posting!!');
                                        $('#editHistoryModal').modal('hide');
                                    },
                                    error: function (error) {
                                        console.error('Error:', error);
                                        alert('Terjadi kesalahan. Silakan coba lagi.');
                                    }
                                });
                            });

                            // Event handler untuk tombol posting
                            $('#postButton').click(function (event) {
                                event.preventDefault();

                                // Data tambahan untuk posting
                                var postedDate = $('#editTanggalMutasi').val();

                                // Permintaan Ajax untuk posting data
                                $.ajax({
                                    type: 'POST',
                                    url: 'proses_post_data.php',
                                    data: { postedDate: postedDate, /* Tambahkan data lain jika diperlukan */ },
                                    success: function (response) {
                                        console.log(response);
                                        alert('Data berhasil diposting!');
                                        $('#editHistoryModal').modal('hide');
                                    },
                                    error: function (error) {
                                        console.error('Error:', error);
                                        alert('Terjadi kesalahan. Silakan coba lagi.');
                                    }
                                });
                            });
                        });
                       
    
                        $('.cancel-btn').click(function () {
    var noMutasi = $(this).data('no-mutasi');
    var tanggalBatal = $(this).data('tanggalbatal');

    // AJAX request untuk membatalkan data
    $.ajax({
        type: 'POST',
        url: 'proses_cancel_data.php',
        data: { noMutasi: noMutasi, tanggalbatal: tanggalBatal },
        dataType: 'json',
        success: function (response) {
            console.log(response);

            if (response.status === 'success') {
                alert('Data berhasil dibatalkan!');
                // Hapus baris tabel atau perbarui antarmuka pengguna jika diperlukan
                $('table').hide();
            } else {
                alert('Gagal membatalkan data. ' + response.message);
            }
        },
        error: function (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi. (AJAX Error)');
        }
    });
});
</script>
                        <script>
                        // Event handler for viewHistoryModal
                        $('#viewHistoryModal').on('show.bs.modal', function (event) {
                            // Close the edit modal if it is open
                            $('#editHistoryModal').modal('hide');

                            var button = $(event.relatedTarget);
                            var noRangka = button.data('no-rangka');

                            // Isi nilai input pada form view
                            $('#noRangkaView').val(noRangka);
                            $('#noMutasiView').val(button.closest('tr').find('td:eq(0)').text());
                            $('#tanggalMutasiView').val(button.closest('tr').find('td:eq(1)').text());
                            $('#CustomerView').val(button.closest('tr').find('td:eq(2)').text());
                            $('#picView').val(button.closest('tr').find('td:eq(3)').text());
                            $('#keteranganView').val(button.closest('tr').find('td:eq(4)').text());
                            $('#postedDateView').val(button.closest('tr').find('td:eq(5)').text());
                        });

                        // Event handler for formViewHistory submission
                        $('#formViewHistory').submit(function (event) {
                            event.preventDefault();
                            // Ajax request for submitting form data
                            $.ajax({
                                type: 'POST',
                                url: 'proses_view_history.php',
                                data: $(this).serialize(),
                                success: function (response) {
                                    console.log(response);
                                    alert('Data berhasil direkam ke tabel history!');

                                    // Tutup modal setelah berhasil direkam
                                    $('#viewHistoryModal').modal('hide');
                                },
                                error: function (error) {
                                    console.error('Error:', error);
                                    alert('Terjadi kesalahan. Silakan coba lagi.');
                                }
                            });
                            });
</script>
</body>
</html>