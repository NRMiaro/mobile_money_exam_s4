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

                <div class="alert alert-info mt-3 mb-4" id="resumeDepot">

                    <div class="d-flex justify-content-between">
                        <span>Frais</span>
                        <strong id="frais">0 Ar</strong>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <span>Montant crédité</span>
                        <strong id="montantCredite">0 Ar</strong>
                    </div>

                </div>

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

<script>
    const baremes = <?= json_encode($baremes) ?>;

    const inputMontant = document.getElementById('montant');

    const fraisLabel = document.getElementById('frais');
    const montantCredite = document.getElementById('montantCredite');

    function formatAr(nombre) {
        return new Intl.NumberFormat('fr-FR').format(nombre) + " Ar";
    }

    function rechercherBareme(montant) {

        for (const bareme of baremes) {

            if (
                montant >= bareme.montant_min &&
                montant <= bareme.montant_max
            ) {
                return bareme;
            }

        }

        return null;
    }

    function mettreAJourResume() {

        const montant = parseFloat(inputMontant.value) || 0;

        const bareme = rechercherBareme(montant);

        let frais = 0;

        if (bareme) {
            frais = parseFloat(bareme.frais);
        }

        fraisLabel.textContent = formatAr(frais);

        montantCredite.textContent =
            formatAr(Math.max(0, montant - frais));

    }

    inputMontant.addEventListener("input", mettreAJourResume);

    mettreAJourResume();
</script>

<?= $this->endSection() ?>