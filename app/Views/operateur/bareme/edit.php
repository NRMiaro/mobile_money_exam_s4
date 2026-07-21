<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<?php
function formatMontant($value): string
{
    return $value !== null
        ? number_format($value, 0, ',', ' ') . ' Ar'
        : 'Illimité';
}

$avant = $contexte['avant'] ?? null;
$courant = $contexte['courant'];
$apres = $contexte['apres'] ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Modifier un barème</h2>
        <p class="text-muted mb-0">
            Les tranches voisines seront ajustées automatiquement.
        </p>
    </div>

    <a href="<?= base_url('operateur/baremes') ?>" class="btn btn-outline-dark fw-semibold">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<form method="post" action="<?= base_url('operateur/baremes/update/' . $courant['id']) ?>">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <?php if ($avant): ?>
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-light fw-bold text-muted small text-uppercase">
                        Barème précédent
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <small class="text-muted d-block">Montant min</small>
                                <span class="fw-semibold"><?= formatMontant($avant['montant_min']) ?></span>
                            </div>
                            <div class="col">
                                <small class="text-muted d-block">Montant max</small>
                                <span class="fw-semibold" id="previewAvantMax"><?= formatMontant($avant['montant_max']) ?></span>
                            </div>
                            <div class="col">
                                <small class="text-muted d-block">Frais</small>
                                <span class="fw-semibold"><?= formatMontant($avant['frais']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Ligne à modifier : Styled MVola -->
            <div class="bareme-edit-card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Barème à modifier</span>
                    <span class="badge bg-warning text-dark">En cours d'édition</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Montant minimum</label>
                            <input
                                type="number"
                                class="form-control"
                                id="montant_min"
                                name="montant_min"
                                value="<?= $courant['montant_min'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Montant maximum</label>
                            <input
                                type="number"
                                class="form-control"
                                id="montant_max"
                                name="montant_max"
                                value="<?= $courant['montant_max'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Frais</label>
                            <input
                                type="number"
                                class="form-control"
                                name="frais"
                                value="<?= $courant['frais'] ?>">
                        </div>
                    </div>
                </div>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2 fw-semibold">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if ($apres): ?>
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-light fw-bold text-muted small text-uppercase">
                        Barème suivant
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <small class="text-muted d-block">Montant min</small>
                                <span class="fw-semibold" id="previewApresMin"><?= formatMontant($apres['montant_min']) ?></span>
                            </div>
                            <div class="col">
                                <small class="text-muted d-block">Montant max</small>
                                <span class="fw-semibold"><?= formatMontant($apres['montant_max']) ?></span>
                            </div>
                            <div class="col">
                                <small class="text-muted d-block">Frais</small>
                                <span class="fw-semibold"><?= formatMontant($apres['frais']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <button class="btn btn-mm-primary w-100 mt-2">
                Enregistrer les modifications
            </button>
        </div>
    </div>
</form>

<script>
    const inputMin = document.getElementById('montant_min');
    const inputMax = document.getElementById('montant_max');
    const previewAvantMax = document.getElementById('previewAvantMax');
    const previewApresMin = document.getElementById('previewApresMin');

    function formatAr(value) {
        return Number(value).toLocaleString('fr-FR') + ' Ar';
    }

    function updatePreview() {
        let min = parseInt(inputMin.value);
        let max = parseInt(inputMax.value);

        if (previewAvantMax && !isNaN(min)) {
            previewAvantMax.innerHTML = formatAr(min - 1);
        }

        if (previewApresMin && !isNaN(max)) {
            previewApresMin.innerHTML = formatAr(max + 1);
        }
    }

    inputMin.addEventListener('input', updatePreview);
    inputMax.addEventListener('input', updatePreview);
</script>

<?= $this->endSection() ?>