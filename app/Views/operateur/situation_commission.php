<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'commission';
$pageTitle  = 'Situation des commissions';
$pageDesc   = 'Commission générée par opérateur partenaire';
?>

<div class="row g-3 mb-4">
    <?php foreach ($commissions as $c): ?>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-ico">&#8644;</div>
                <div>
                    <div class="stat-value"><?= number_format($c['total_commission'], 0, ',', ' ') ?> Ar</div>
                    <div class="stat-label"><?= esc($c['operateur']) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="mm-table-card">
    <div class="card-head">
        <h2>Détail par opérateur</h2>
    </div>
    <table class="table mm-table">
        <thead>
            <tr>
                <th>Opérateur</th>
                <th>Total commissions dues</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commissions as $c): ?>
                <tr>
                    <td><?= esc($c['operateur']) ?></td>
                    <td><?= number_format($c['total_commission'], 0, ',', ' ') ?> Ar</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>