<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'historique';
$pageTitle  = 'Historique';
$pageDesc   = 'Toutes vos transactions';

// Données en dur pour l'intégration
$transactions = [
    ['date' => '19/07/2026 14:32', 'type' => 'depot',     'montant' => 50000,  'frais' => 0,   'sens' => '+'],
    ['date' => '18/07/2026 09:10', 'type' => 'transfert', 'montant' => 15000,  'frais' => 300, 'sens' => '-'],
    ['date' => '16/07/2026 18:45', 'type' => 'retrait',   'montant' => 20000,  'frais' => 500, 'sens' => '-'],
    ['date' => '12/07/2026 11:02', 'type' => 'depot',     'montant' => 100000, 'frais' => 0,   'sens' => '+'],
    ['date' => '05/07/2026 16:20', 'type' => 'transfert', 'montant' => 8000,   'frais' => 200, 'sens' => '-'],
];
$labels = ['depot' => 'Dépôt', 'retrait' => 'Retrait', 'transfert' => 'Transfert'];
?>

<div class="mm-table-card">
    <div class="card-head">
        <h2>Historique des transactions</h2>
    </div>
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
</div>

<?= $this->endSection() ?>
