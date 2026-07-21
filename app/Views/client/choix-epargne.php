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
            <div class="op-title">Modifier son epargne</div>
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

        <form action="<?= base_url('client/choix-epargne') ?>" method="post">

            <?= csrf_field() ?>

            <div class="mb-4">

                <label class="form-label" for="montant">
                    Nouveau pourcentage d'epargne
                </label>

                <div class="amount-input-group">

                    <input
                        type="number"
                        class="form-control"
                        id="pourcentage"
                        name="pourcentage"
                        placeholder="Ex : 10%"
                        min="0"
                        max="100"
                        step="1"
                        value="<?= $pourcentageEpargne ?? 0 ?>"
                        required>

                    <span class="amount-suffix">
                        Ar
                    </span>

                </div>

                <small class="text-muted">
                    Chaque pourcentage de transfert recu ira dans l'epargne
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

                    Mettre a jour

                </button>

            </div>

        </form>

    </div>

</div>

<?= $this->endSection() ?>
