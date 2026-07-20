<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'prefixes';
$pageTitle  = 'Ajouter un préfixe';
$pageDesc   = 'Nouveau préfixe valable pour l\'opérateur';
?>

<div class="op-form-card">
    <div class="op-form-head head-transfert">
        <div class="op-ico">&#9741;</div>
        <div>
            <div class="op-title">Nouveau préfixe</div>
            <div class="op-sub">Ex : 033, 037, 038...</div>
        </div>
    </div>

    <div class="op-form-body">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('operateur/prefixes/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="prefixe" class="form-label">Préfixe</label>
                <input type="text" class="form-control" id="prefixe" name="prefixe"
                       placeholder="033" maxlength="3" pattern="[0-9]{3}" required>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('operateur/prefixes') ?>" class="btn btn-outline-secondary flex-fill">Annuler</a>
                <button type="submit" class="btn btn-mm-primary flex-fill">Valider</button>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>
