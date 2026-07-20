<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'baremes';
$pageTitle  = 'Ajouter un barème';
$pageDesc   = 'Nouvelle tranche de frais pour un type d\'opération';

// Données en dur pour l'intégration - viendra de $typesTransaction dans le controller
$typesTransaction = [
    ['id' => 1, 'libelle' => 'depot',     'label' => 'Dépôt'],
    ['id' => 2, 'libelle' => 'retrait',   'label' => 'Retrait'],
    ['id' => 3, 'libelle' => 'transfert', 'label' => 'Transfert'],
];

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
                        <option value="<?= $type['id'] ?>"><?= esc($type['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label for="montant_min" class="form-label">Montant min</label>
                    <div class="amount-input-group">
                        <input type="number" class="form-control" id="montant_min" name="montant_min"
                               placeholder="0" min="0" step="1" required style="font-size:1rem;padding:.55rem 2.6rem .55rem .8rem;">
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
                    <div class="form-text">Laisser vide pour "illimité"</div>
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

<?= $this->endSection() ?>
