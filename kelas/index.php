<?php
$pageTitle = 'Data Kelas';
require_once __DIR__ . '/../template/header.php';

$totalKelas = mysqli_fetch_assoc(
    mysqli_query($connect, "SELECT COUNT(*) AS total FROM kelas")
);
$totalNamaKelas = mysqli_fetch_assoc(
    mysqli_query($connect, "SELECT COUNT(DISTINCT nama_kelas) AS total FROM kelas")
);
$data = mysqli_query(
    $connect,
    "SELECT id_kelas, nama_kelas, tingkat, bagian
     FROM kelas
     ORDER BY FIELD(tingkat, 'X', 'XI', 'XII'), nama_kelas ASC, id_kelas ASC"
);
$dataCount = mysqli_num_rows($data);
$canManage = $isPetugas || $isAdmin;
?>

<link rel="stylesheet" href="<?= asset('css/data.css') ?>">

<div class="container-fluid">

    <?php if (($_GET['pesan'] ?? '') === 'simpan'): ?>
        <div class="alert alert-success">Data kelas berhasil ditambahkan.</div>
    <?php elseif (($_GET['pesan'] ?? '') === 'update'): ?>
        <div class="alert alert-success">Data kelas berhasil diperbarui.</div>
    <?php elseif (($_GET['pesan'] ?? '') === 'hapus'): ?>
        <div class="alert alert-success">Data kelas berhasil dihapus.</div>
    <?php elseif (($_GET['pesan'] ?? '') === 'gagal'): ?>
        <div class="alert alert-danger">Terjadi kesalahan.</div>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Kelas</h3>
            <p>Kelola seluruh data kelas musholla.</p>
        </div>

        <?php if ($canManage): ?>
            <a href="<?= url('kelas/tambah.php') ?>" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Tambah Kelas
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="stats-info">
                    <small>Total Data Kelas</small>
                    <h2><?= (int) ($totalKelas['total'] ?? 0) ?></h2>
                    <span>Data</span>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="stats-info">
                    <small>Total Nama Kelas</small>
                    <h2><?= (int) ($totalNamaKelas['total'] ?? 0) ?></h2>
                    <span>Kelas</span>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama kelas, tingkat, atau bagian...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Bagian</th>
                        <?php if ($canManage): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dataCount > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <?php
                            $badgeClass = preg_replace(
                                '/[^a-zA-Z0-9_-]/',
                                '-',
                                (string) $row['nama_kelas']
                            );
                            $badgeClass = $badgeClass !== '' ? $badgeClass : 'kelas';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="role-badge JURUSAN-<?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars((string) $row['nama_kelas'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td><strong><?= htmlspecialchars((string) $row['tingkat'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td>
                                    <?= $row['bagian'] !== null && $row['bagian'] !== ''
                                        ? htmlspecialchars((string) $row['bagian'], ENT_QUOTES, 'UTF-8')
                                        : '-' ?>
                                </td>
                                <?php if ($canManage): ?>
                                    <td>
                                        <div class="action-group">
                                            <a href="<?= url('kelas/edit.php?id=' . (int) $row['id_kelas']) ?>" class="btn-edit">
                                                <i class="fas fa-pen"></i> Edit
                                            </a>
                                            <a href="<?= url('kelas/hapus.php?id=' . (int) $row['id_kelas']) ?>" class="btn-delete"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $canManage ? 5 : 4 ?>">
                                <div class="empty-data">
                                    <i class="fas fa-folder-open"></i>
                                    <h4>Belum Ada Data Kelas</h4>
                                    <p>Silakan tambahkan data kelas pertama.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="table-info">
                Total Data:
                <strong><?= $dataCount ?></strong>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/script.js') ?>"></script>
<?php require_once __DIR__ . '/../template/footer.php'; ?>
