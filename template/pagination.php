<?php
if (!isset($page, $totalHalaman) || $totalHalaman <= 1) {
    return;
}

$paginationParams = $paginationParams ?? $_GET;
unset($paginationParams['halaman'], $paginationParams['pesan']);

$paginationQuery = http_build_query($paginationParams);
$paginationBase = basename($_SERVER['PHP_SELF']) . '?' . $paginationQuery;

if ($paginationQuery !== '') {
    $paginationBase .= '&';
}
?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="<?= htmlspecialchars($paginationBase . 'halaman=' . ($page - 1), ENT_QUOTES, 'UTF-8'); ?>">&laquo;</a>
    <?php else: ?>
        <span>&laquo;</span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
        <?php if ($i == $page): ?>
            <span class="active"><?= $i; ?></span>
        <?php else: ?>
            <a href="<?= htmlspecialchars($paginationBase . 'halaman=' . $i, ENT_QUOTES, 'UTF-8'); ?>"><?= $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalHalaman): ?>
        <a href="<?= htmlspecialchars($paginationBase . 'halaman=' . ($page + 1), ENT_QUOTES, 'UTF-8'); ?>">&raquo;</a>
    <?php else: ?>
        <span>&raquo;</span>
    <?php endif; ?>
</div>
