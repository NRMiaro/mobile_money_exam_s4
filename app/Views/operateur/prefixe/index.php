<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'prefixes';
$pageTitle  = 'Préfixes';
$pageDesc   = 'Préfixes valables pour cet opérateur';
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success py-2 small"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="mm-table-card">
    <div class="card-head">
        <h2>Liste des préfixes</h2>
        <a href="<?= base_url('operateur/prefixes/create') ?>" class="btn btn-mm-accent btn-sm">+ Ajouter un préfixe</a>
    </div>

    <?php if (empty($prefixes)): ?>
        <div class="p-4 text-center text-muted small">Aucun préfixe configuré pour le moment.</div>
    <?php else: ?>
        <table class="table mm-table">
            <thead>
            <tr>
                <th>Référence</th>
                <th>Préfixe</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($prefixes as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td class="fw-semibold"><?= esc($p['prefixe']) ?></td>
                    
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>