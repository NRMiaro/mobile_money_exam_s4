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

            <div class="alert alert-light border mb-4">

                <div class="form-check mb-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="payerRetrait"
                        name="payer_retrait">

                    <label class="form-check-label" for="payerRetrait">
                        Prendre en charge les frais de retrait du destinataire
                    </label>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Frais de retrait estimés</span>
                    <strong id="fraisRetrait">0 Ar</strong>
                </div>

                <small id="messageRetrait" class="text-muted">
                    Disponible uniquement pour les transferts vers le même opérateur.
                </small>

            </div>

            <div
                id="blocCommission"
                class="d-flex justify-content-between mt-2 d-none">

                <span>Commission inter-opérateur</span>

                <strong id="commission">
                    0 Ar
                </strong>

            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('client/dashboard') ?>" class="btn btn-outline-secondary flex-fill">Annuler</a>
                <button type="submit" class="btn btn-mm-primary flex-fill">Valider</button>
            </div>
        </form>

    </div>
</div>

<script>
    const commissions = <?= json_encode($commissions) ?>;
    const prefixes = <?= json_encode($prefixes) ?>;
    const idOperateur = <?= $idOperateur ?>;
    const baremes = <?= json_encode($baremes) ?>;
    const baremesRetrait = <?= json_encode($baremesRetrait) ?>;

    const numeroInput = document.getElementById("numero_destinataire");
    const montantInput = document.getElementById("montant");

    const fraisLabel = document.getElementById("frais");
    const montantEnvoye = document.getElementById("montantEnvoye");
    const totalDebite = document.getElementById("totalDebite");

    const checkboxRetrait = document.getElementById("payerRetrait");
    const fraisRetraitLabel = document.getElementById("fraisRetrait");
    const messageRetrait = document.getElementById("messageRetrait");

    const commissionBloc = document.getElementById("blocCommission");
    const commissionLabel = document.getElementById("commission");

    function formatAr(nombre) {
        return new Intl.NumberFormat("fr-FR").format(nombre) + " Ar";
    }

    function chercherBareme(montant, liste) {

        for (const b of liste) {
            if (
                montant >= b.montant_min &&
                montant <= b.montant_max
            ) {
                return b;
            }
        }

        return null;
    }

    function trouverOperateur(numero) {

        const prefixe = numero.substring(0, 3);
        const resultat = prefixes.find(
            p => p.prefixe === prefixe
        );

        return resultat ? resultat.id_operateur : null;
    }

    function verifierOperateurDestinataire() {

        const numero = numeroInput.value.replace(/\s/g, '');

        if (numero.length < 3) {

            checkboxRetrait.disabled = true;

            messageRetrait.textContent =
                "Saisissez un numéro valide.";

            return;
        }


        const operateurDestinataire = trouverOperateur(numero);


        if (!operateurDestinataire) {

            checkboxRetrait.disabled = true;

            messageRetrait.textContent =
                "Opérateur inconnu.";

            return;
        }


        // Même opérateur
        if (operateurDestinataire == idOperateur) {

            checkboxRetrait.disabled = false;

            messageRetrait.textContent =
                "Vous pouvez prendre en charge les frais de retrait.";

            return;
        }


        // Autre opérateur

        checkboxRetrait.checked = false;
        checkboxRetrait.disabled = true;

        fraisRetraitLabel.textContent =
            "Non disponible";


        messageRetrait.textContent =
            "Les frais de retrait ne sont pas disponibles pour un transfert inter-opérateur.";
    }

    function calculerCommission(montant) {

        const numero = numeroInput.value.replace(/\s/g, '');

        const operateurDestinataire =
            trouverOperateur(numero);


        if (!operateurDestinataire ||
            operateurDestinataire == idOperateur) {

            commissionBloc.classList.add("d-none");
            return 0;
        }


        const commission =
            commissions.find(
                c => c.id_operateur == operateurDestinataire
            );


        if (!commission) {

            commissionBloc.classList.add("d-none");
            return 0;
        }


        const montantCommission =
            montant * commission.pct_commission / 100;


        commissionBloc.classList.remove("d-none");

        commissionLabel.textContent =
            formatAr(montantCommission);


        return montantCommission;
    }

    function actualiser() {

        verifierOperateurDestinataire();

        const montant = parseFloat(montantInput.value) || 0;

        const baremeTransfert = chercherBareme(montant, baremes);
        const fraisTransfert = baremeTransfert ?
            parseFloat(baremeTransfert.frais) :
            0;

        const baremeRetrait = chercherBareme(montant, baremesRetrait);
        const fraisRetrait = baremeRetrait ?
            parseFloat(baremeRetrait.frais) :
            0;

        const commission = calculerCommission(montant);

        fraisLabel.textContent = formatAr(fraisTransfert);
        montantEnvoye.textContent = formatAr(montant);

        if (checkboxRetrait.checked && !checkboxRetrait.disabled) {
            fraisRetraitLabel.textContent = formatAr(fraisRetrait);
        } else {
            fraisRetraitLabel.textContent = "Non inclus";
        }

        let total =
            montant +
            fraisTransfert +
            commission;

        if (checkboxRetrait.checked) {
            total += fraisRetrait;
        }

        totalDebite.textContent = formatAr(total);
    }

    montantInput.addEventListener("input", actualiser);
    numeroInput.addEventListener("input", actualiser);
    checkboxRetrait.addEventListener("change", actualiser);

    actualiser();
</script>

<?= $this->endSection() ?>