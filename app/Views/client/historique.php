<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'historique';
$pageTitle  = 'Historique';
$pageDesc   = 'Toutes vos transactions';

$labels = ['depot' => 'Dépôt', 'retrait' => 'Retrait', 'transfert' => 'Transfert'];
?>

<div class="mm-table-card">
    <div class="card-head">
        <h2>Historique des transactions</h2>
    </div>

    <?php if (empty($transactions)): ?>
        <div class="p-4 text-center text-muted small">Aucune transaction pour le moment.</div>
    <?php else: ?>
        <table class="table mm-table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Frais</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= esc($t['date']) ?></td>
                    <td><span class="badge-op <?= $t['type'] ?>"><?= $labels[$t['type']] ?></span></td>
                    <td class="fw-semibold"><?= $t['sens'] ?><?= number_format($t['montant'], 0, ',', ' ') ?> Ar</td>
                    <td><?= number_format($t['frais'], 0, ',', ' ') ?> Ar</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>