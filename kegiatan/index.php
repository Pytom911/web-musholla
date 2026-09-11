<?php
$pageTitle = 'Data Kegiatan';
require_once '../template/header.php';

$totalKegiatan = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM kegiatan"));
$totalPengeluaran = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(pengeluaran),0) AS total FROM kegiatan"));
$data = mysqli_query($connect, "SELECT * FROM kegiatan ORDER BY id_kegiatan DESC");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == "simpan"): ?>
            <div class="alert alert-success">Data kegiatan berhasil ditambahkan.</div>
        <?php elseif ($_GET['pesan'] == "update"): ?>
            <div class="alert alert-success">Data kegiatan berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == "hapus"): ?>
            <div class="alert alert-success">Data kegiatan berhasil dihapus.</div>
        <?php elseif ($_GET['pesan'] == "gagal"): ?>
            <div class="alert alert-danger">Terjadi kesalahan.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Kegiatan</h3>
            <p>Kelola seluruh data kegiatan dan pengeluaran musholla.</p>
        </div>
        <?php if ($isPetugas || $isAdmin): ?>
            <a href="tambah.php" class="btn-add">
                <i class="bi bi-plus-circle-fill"></i>
                Tambah Kegiatan
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div class="stats-info">
                    <small>Total Kegiatan</small>
                    <h2><?= $totalKegiatan['total'] ?? 0; ?></h2>
                    <span>Seluruh Kegiatan</span>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stats-info">
                    <small>Total Pengeluaran</small>
                    <h2>Rp<?= number_format($totalPengeluaran['total'] ?? 0, 0, ',', '.'); ?></h2>
                    <span>Semua Kegiatan</span>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama kegiatan...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kegiatan</th>
                        <th>Pengeluaran</th>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <?php if ($isPetugas || $isAdmin): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr data-tanggal="<?= htmlspecialchars($row['tanggal']); ?>">
                                <td><?= $no++; ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_kegiatan']); ?></strong></td>
                                <td>
                                    <span class="nominal-red">
                                        Rp <?= number_format($row['pengeluaran'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td><?= date('d F Y', strtotime($row['tanggal'])); ?></td>
                                <td><strong><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></strong></td>
                                <?php if ($isPetugas || $isAdmin): ?>
                                    <td>
                                        <div class="action-group">
                                            <a href="edit.php?id=<?= $row['id_kegiatan']; ?>" class="btn-edit">
                                                <i class="bi bi-pencil-fill"></i> Edit
                                            </a>
                                            <a href="hapus.php?id=<?= $row['id_kegiatan']; ?>" class="btn-delete" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
                                                <i class="bi bi-trash-fill"></i>
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-data">
                                    <i class="bi bi-folder2-open"></i>
                                    <h4>Belum Ada Data Kegiatan</h4>
                                    <p>Silakan tambahkan kegiatan baru untuk mulai mengelola data.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="table-info">
                Total Data :
                <strong><?= mysqli_num_rows($data); ?></strong>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
<?php require_once '../template/footer.php'; ?>