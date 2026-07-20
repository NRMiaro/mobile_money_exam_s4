<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'dashboard';
$pageTitle  = 'Retrait';
$pageDesc   = 'Retirer des fonds de votre compte';
?>

<div class="op-form-card">
    <div class="op-form-head head-retrait">
        <div class="op-ico">&#8593;</div>
        <div>
            <div class="op-title">Faire un retrait</div>
            <div class="op-sub">Des frais peuvent s'appliquer selon le barème</div>
        </div>
    </div>

    <div class="op-form-body">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success py-2 small"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('client/retrait') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="montant" class="form-label">Montant à retirer</label>
                <div class="amount-input-group">
                    <input type="number" class="form-control" id="montant" name="montant"
                        placeholder="0" min="1" step="1" required>
                    <span class="amount-suffix">Ar</span>
                </div>

                <div class="alert alert-info mt-3 mb-4">

                    <div class="d-flex justify-content-between">
                        <span>Frais</span>
                        <strong id="frais">0 Ar</strong>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <span>Montant débité du compte</span>
                        <strong id="total">0 Ar</strong>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <span>Vous recevrez</span>
                        <strong id="recu">0 Ar</strong>
                    </div>

                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-outline-secondary flex-fill">Annuler</a>
                <button type="submit" class="btn btn-mm-primary flex-fill">Valider</button>
            </div>
        </form>

    </div>
</div>

<script>
    const baremes = <?= json_encode($baremes) ?>;

    const montantInput = document.getElementById("montant");

    const fraisLabel = document.getElementById("frais");
    const totalLabel = document.getElementById("total");
    const recuLabel = document.getElementById("recu");

    function formatAr(nombre) {
        return new Intl.NumberFormat("fr-FR").format(nombre) + " Ar";
    }

    function chercherBareme(montant) {

        for (const b of baremes) {

            if (
                montant >= b.montant_min &&
                montant <= b.montant_max
            ) {
                return b;
            }

        }

        return null;
    }

    function actualiser() {

        const montant = parseFloat(montantInput.value) || 0;

        const bareme = chercherBareme(montant);

        const frais = bareme ? parseFloat(bareme.frais) : 0;

        fraisLabel.textContent = formatAr(frais);

        totalLabel.textContent = formatAr(montant + frais);

        recuLabel.textContent = formatAr(montant);

    }

    montantInput.addEventListener("input", actualiser);

    actualiser();
</script>

<?= $this->endSection() ?>