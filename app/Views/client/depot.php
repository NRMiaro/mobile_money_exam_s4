<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'depot';
$pageTitle  = 'Dépôt';
$pageDesc   = 'Créditez instantanément votre compte Mobile Money';
?>

<div class="op-form-card">

    <div class="op-form-head head-depot">
        <div class="op-ico">&#8595;</div>
        <div>
            <div class="op-title">Faire un dépôt</div>
            <div class="op-sub">
                Le montant sera ajouté immédiatement à votre solde.
            </div>
        </div>
    </div>

    <div class="op-form-body">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success py-2">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('client/depot') ?>" method="post">

            <?= csrf_field() ?>

            <div class="mb-4">

                <label class="form-label" for="montant">
                    Montant à déposer
                </label>

                <div class="amount-input-group">

                    <input
                        type="number"
                        class="form-control"
                        id="montant"
                        name="montant"
                        placeholder="Ex : 100000"
                        min="1"
                        step="1"
                        required>

                    <span class="amount-suffix">
                        Ar
                    </span>

                </div>

                <small class="text-muted">
                    Le dépôt est crédité automatiquement sur votre compte.
                </small>

            </div>

            <div class="d-flex gap-2">

                <a href="<?= base_url('client/dashboard') ?>"
                   class="btn btn-outline-secondary flex-fill">
                    Annuler
                </a>

                <button
                    type="submit"
                    class="btn btn-success flex-fill">

                    Effectuer le dépôt

                </button>

            </div>

        </form>

    </div>

</div>

<?= $this->endSection() ?>