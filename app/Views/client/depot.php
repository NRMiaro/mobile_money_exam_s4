<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'dashboard';
$pageTitle  = 'Dépôt';
$pageDesc   = 'Créditer votre compte mobile money';
?>

<div class="op-form-card">
    <div class="op-form-head head-depot">
        <div class="op-ico">&#8595;</div>
        <div>
            <div class="op-title">Faire un dépôt</div>
            <div class="op-sub">Le crédit est appliqué automatiquement</div>
        </div>
    </div>

    <div class="op-form-body">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success py-2 small"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('client/depot') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="montant" class="form-label">Montant à déposer</label>
                <div class="amount-input-group">
                    <input type="number" class="form-control" id="montant" name="montant"
                           placeholder="0" min="1" step="1" required>
                    <span class="amount-suffix">Ar</span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-outline-secondary flex-fill">Annuler</a>
                <button type="submit" class="btn btn-mm-primary flex-fill">Valider</button>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>
