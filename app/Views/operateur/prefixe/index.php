<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'prefixes';
$pageTitle  = 'Préfixes';
$pageDesc   = 'Préfixes valables pour cet opérateur';

// Données en dur pour l'intégration
$prefixes = [
    ['id' => 1, 'prefixe' => '033'],
    ['id' => 2, 'prefixe' => '037'],
    ['id' => 3, 'prefixe' => '038'],
];
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success py-2 small"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="mm-table-card">
    <div class="card-head">
        <h2>Liste des préfixes</h2>
        <a href="<?= base_url('operateur/prefixes/create') ?>" class="btn btn-mm-accent btn-sm">+ Ajouter un préfixe</a>
    </div>
    <table class="table mm-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Préfixe</th>
            <th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($prefixes as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td class="fw-semibold"><?= esc($p['prefixe']) ?></td>
                <td class="text-end">
                    <a href="<?= base_url('operateur/prefixes/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                    <a href="<?= base_url('operateur/prefixes/delete/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Supprimer ce préfixe ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
