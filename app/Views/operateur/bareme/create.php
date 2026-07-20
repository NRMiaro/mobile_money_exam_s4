<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'baremes';
$pageTitle  = 'Ajouter un barème';
$pageDesc   = 'Nouvelle tranche de frais pour un type d\'opération';

?>

<div class="op-form-card">
    <div class="op-form-head head-transfert">
        <div class="op-ico">&#8801;</div>
        <div>
            <div class="op-title">Nouveau barème</div>
            <div class="op-sub">Définir une tranche de frais</div>
        </div>
    </div>

    <div class="op-form-body">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('operateur/baremes/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="id_type_transaction" class="form-label">Type de transaction</label>
                <select class="form-select" id="id_type_transaction" name="id_type_transaction" required>
                    <option value="" selected disabled>-- Choisir un type --</option>
                    <?php foreach ($typesTransaction as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= esc(ucfirst($type['libelle'])) ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="baremes-container" class="mb-3 d-none">
                    <label class="form-label">Barèmes existants</label>

                    <div id="baremes-list" class="small"></div>

                    <div id="baremes-message" class="alert alert-warning mt-2">
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label for="montant_min" class="form-label">Montant min</label>
                    <div class="amount-input-group">
                        <input type="number" class="form-control" id="montant_min" name="montant_min"
                            placeholder="0" min="0" step="1" required style="font-size:1rem;padding:.55rem 2.6rem .55rem .8rem;" readonly>
                        <span class="amount-suffix" style="right:12px;font-size:.82rem;">Ar</span>
                    </div>
                </div>
                <div class="col-6">
                    <label for="montant_max" class="form-label">Montant max</label>
                    <div class="amount-input-group">
                        <input type="number" class="form-control" id="montant_max" name="montant_max"
                            placeholder="Illimité" min="0" step="1" style="font-size:1rem;padding:.55rem 2.6rem .55rem .8rem;">
                        <span class="amount-suffix" style="right:12px;font-size:.82rem;">Ar</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="frais" class="form-label">Frais</label>
                <div class="amount-input-group">
                    <input type="number" class="form-control" id="frais" name="frais"
                        placeholder="0" min="0" step="1" required>
                    <span class="amount-suffix">Ar</span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('operateur/baremes') ?>" class="btn btn-outline-secondary flex-fill">Annuler</a>
                <button type="submit" class="btn btn-mm-primary flex-fill">Valider</button>
            </div>
        </form>

    </div>
</div>

<script>
const baremes = <?= json_encode($baremes) ?>;

const selectType = document.getElementById('id_type_transaction');
const montantMin = document.getElementById('montant_min');
const montantMax = document.getElementById('montant_max');

const container = document.getElementById('baremes-container');
const list = document.getElementById('baremes-list');
const message = document.getElementById('baremes-message');


selectType.addEventListener('change', function() {

    const idType = this.value;

    list.innerHTML = '';
    message.innerHTML = '';

    if (!baremes[idType]) {
        container.classList.add('d-none');
        montantMin.value = '';
        return;
    }


    const liste = baremes[idType];

    container.classList.remove('d-none');


    let dernierMax = 0;


    liste.forEach(b => {

        dernierMax = Math.max(
            dernierMax,
            parseInt(b.montant_max ?? 0)
        );


        list.innerHTML += `
            <div class="border rounded p-2 mb-1">
                ${b.montant_min} Ar -
                ${b.montant_max ?? 'Illimité'} Ar
                :
                Frais de ${b.frais} Ar
            </div>
        `;
    });


    const prochainMin = dernierMax + 1;


    montantMin.value = prochainMin;
    montantMin.min = prochainMin;
    montantMax.value = prochainMin + 1;
    montantMax.min = prochainMin + 1;


    message.innerHTML =
        `
        Le nouveau barème doit commencer à partir de 
        <strong>${prochainMin} Ar</strong>.
        <br>
        Veuillez modifier les barèmes existants si vous souhaitez
        créer une tranche inférieure.
        `;

});
</script>

<?= $this->endSection() ?>