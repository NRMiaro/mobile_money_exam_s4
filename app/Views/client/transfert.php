<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'dashboard';
$pageTitle  = 'Transfert';
$pageDesc   = 'Envoyer de l\'argent à un autre numéro';
?>

<div class="op-form-card">
    <div class="op-form-head head-transfert">
        <div class="op-ico">&#8644;</div>
        <div>
            <div class="op-title">Faire un transfert</div>
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

        <form action="<?= base_url('client/transfert') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="numero_destinataire" class="form-label">Numéro destinataire</label>
                <input type="text" class="form-control" id="numero_destinataire" name="numero_destinataire"
                    placeholder="037 98 765 43" required>
            </div>

            <div class="mb-4">
                <label for="montant" class="form-label">Montant à transférer</label>
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
                        <span>Montant envoyé</span>
                        <strong id="montantEnvoye">0 Ar</strong>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <span>Total débité</span>
                        <strong id="totalDebite">0 Ar</strong>
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
    const montantEnvoye = document.getElementById("montantEnvoye");
    const totalDebite = document.getElementById("totalDebite");

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

        montantEnvoye.textContent = formatAr(montant);

        totalDebite.textContent = formatAr(montant + frais);

    }

    montantInput.addEventListener("input", actualiser);

    actualiser();
</script>

<?= $this->endSection() ?>