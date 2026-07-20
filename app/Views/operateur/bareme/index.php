<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'baremes';
$pageTitle  = 'Barèmes';
$pageDesc   = 'Frais par tranche de montant, par type d\'opération';

// Données en dur pour l'intégration
$baremes = [
    ['id' => 1, 'type' => 'depot',     'montant_min' => 0,     'montant_max' => null,   'frais' => 0],
    ['id' => 2, 'type' => 'retrait',   'montant_min' => 0,     'montant_max' => 10000,  'frais' => 200],
    ['id' => 3, 'type' => 'retrait',   'montant_min' => 10001, 'montant_max' => 50000,  'frais' => 500],
    ['id' => 4, 'type' => 'retrait',   'montant_min' => 50001, 'montant_max' => null,   'frais' => 1000],
    ['id' => 5, 'type' => 'transfert', 'montant_min' => 0,     'montant_max' => 20000,  'frais' => 200],
    ['id' => 6, 'type' => 'transfert', 'montant_min' => 20001, 'montant_max' => null,   'frais' => 500],
];
$labels = ['depot' => 'Dépôt', 'retrait' => 'Retrait', 'transfert' => 'Transfert'];
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success py-2 small"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="mm-table-card">
    <div class="card-head">
        <h2>Liste des barèmes</h2>
        <a href="<?= base_url('operateur/baremes/create') ?>" class="btn btn-mm-accent btn-sm">+ Ajouter un barème</a>
    </div>
    <table class="table mm-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Type d'opération</th>
            <th>Montant min</th>
            <th>Montant max</th>
            <th>Frais</th>
            <th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($baremes as $b): ?>
            <tr>
                <td><?= $b['id'] ?></td>
                <td><span class="badge-op <?= $b['type'] ?>"><?= $labels[$b['type']] ?></span></td>
                <td><?= number_format($b['montant_min'], 0, ',', ' ') ?> Ar</td>
                <td><?= $b['montant_max'] !== null ? number_format($b['montant_max'], 0, ',', ' ') . ' Ar' : 'Illimité' ?></td>
                <td class="fw-semibold"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                <td class="text-end">
                    <a href="<?= base_url('operateur/baremes/edit/' . $b['id']) ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                    <a href="<?= base_url('operateur/baremes/delete/' . $b['id']) ?>" class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Supprimer ce barème ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
