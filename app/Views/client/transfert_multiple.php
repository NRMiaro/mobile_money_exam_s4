<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'dashboard';
$pageTitle  = 'Transfert multiple';
$pageDesc   = 'Envoyer de l\'argent à plusieurs numéros';
?>

<div class="op-form-card">

    <div class="op-form-head head-transfert">
        <div class="op-ico">&#8644;</div>

        <div>
            <div class="op-title">
                Transfert multiple
            </div>

            <div class="op-sub">
                Le montant sera divisé entre les destinataires
            </div>
        </div>
    </div>
    <div class="op-form-body">
        <?php if (session()->getFlashdata('error')): ?>

            <div class="alert alert-danger py-2 small">
                <?= session()->getFlashdata('error') ?>
            </div>

        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>

            <div class="alert alert-success py-2 small">
                <?= session()->getFlashdata('success') ?>
            </div>

        <?php endif; ?>

        <form action="<?= base_url('client/transfert-multiple') ?>" method="post">

            <?= csrf_field() ?>
            <div class="mb-4">

                <label class="form-label">
                    Montant total
                </label>
                <div class="amount-input-group">
                    <input
                        type="number"
                        class="form-control"
                        id="montant"
                        name="montant"
                        min="1"
                        required>

                    <span class="amount-suffix">
                        Ar
                    </span>
                </div>
            </div>

            <label class="form-label">
                Numéros destinataires
            </label>

            <div id="destinataires">
                <div class="input-group mb-2">
                    <input
                        type="text"
                        name="numeros[]"
                        class="form-control"
                        placeholder="034 00 000 00"
                        required>
                </div>
            </div>

            <button
                type="button"
                id="ajouter"
                class="btn btn-outline-primary btn-sm mb-4">
                + Ajouter un numéro
            </button>
            <div class="alert alert-info">
                <div class="d-flex justify-content-between">
                    <span>
                        Nombre destinataires
                    </span>
                    <strong id="nombre">
                        1
                    </strong>
                </div>

                <div class="d-flex justify-content-between mt-2">
                    <span>
                        Montant par personne
                    </span>
                    <strong id="part">
                        0 Ar
                    </strong>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>
                        Frais transfert
                    </span>
                    <strong id="frais">
                        0 Ar
                    </strong>
                </div>

                <div class="d-flex justify-content-between mt-2">
                    <span>
                        Total débité
                    </span>
                    <strong id="total">
                        0 Ar
                    </strong>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a
                    href="<?= base_url('client/dashboard') ?>"
                    class="btn btn-outline-secondary flex-fill">

                    Annuler

                </a>
                <button
                    class="btn btn-mm-primary flex-fill">
                    Envoyer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const baremes = <?= json_encode($baremes) ?>;
    const commissions = <?= json_encode($commissions) ?>;


    const montant = document.getElementById('montant');
    const destinataires = document.getElementById('destinataires');
    const ajouter = document.getElementById('ajouter');
    const nombre = document.getElementById('nombre');
    const part = document.getElementById('part');

    function formatAr(value) {
        return new Intl.NumberFormat('fr-FR')
            .format(value) + " Ar";
    }

    function actualiser() {

        let nb = document
            .querySelectorAll('input[name="numeros[]"]')
            .length;
        nombre.textContent = nb;

        let montantTotal =
            parseFloat(montant.value) || 0;

        let montantPart = montantTotal / nb;

        part.textContent = formatAr(montantPart);

        let bareme = chercherBareme(montantTotal);
        let frais =
            bareme ?
            parseFloat(bareme.frais) :
            0;
        document.getElementById('frais')
            .textContent =
            formatAr(frais);
        document.getElementById('total')
            .textContent =
            formatAr(
                montantTotal + frais
            );
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

    ajouter.addEventListener('click', () => {
        let div = document.createElement('div');
        div.className = "input-group mb-2";
        div.innerHTML = `
        <input
            type="text"
            name="numeros[]"
            class="form-control"
            placeholder="034 00 000 00"
            required>


        <button
            type="button"
            class="btn btn-danger supprimer">
            X
        </button>

    `;

        destinataires.appendChild(div);
        div.querySelector('.supprimer')
            .addEventListener('click', () => {
                div.remove();
                actualiser();
            });
        actualiser();
    });
    montant.addEventListener('input', actualiser);
    actualiser();
</script>
<?= $this->endSection() ?>