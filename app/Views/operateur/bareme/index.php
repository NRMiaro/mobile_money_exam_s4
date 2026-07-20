<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'baremes';
$pageTitle  = 'Barèmes';
$pageDesc   = 'Frais par tranche de montant, par type d\'opération';

// Données en dur pour l'intégration - à remplacer par 3 requêtes
// (v_bareme_depot, v_bareme_retrait, v_bareme_transaction) depuis le controller

function renderBaremeRows(array $baremes): void
{
    if (empty($baremes)) {
        echo '<div class="p-4 text-center text-muted small">Aucun barème configuré pour ce type d\'opération.</div>';
        return;
    }
    ?>
    <table class="table mm-table">
        <thead>
        <tr>
            <th>Montant min</th>
            <th>Montant max</th>
            <th>Frais</th>
            <th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($baremes as $b): ?>
            <tr>
                <td><?= number_format($b['montant_min'], 0, ',', ' ') ?> Ar</td>
                <td><?= $b['montant_max'] !== null ? number_format($b['montant_max'], 0, ',', ' ') . ' Ar' : 'Illimité' ?></td>
                <td class="fw-semibold"><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</td>
                <td class="text-end">
                    <a href="<?= base_url('operateur/baremes/edit/' . $b['id']) ?>" class="btn btn-sm btn-outline-secondary">Modifier</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success py-2 small"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

    <!-- Segmented control : bascule entre les 3 types de barème -->
    <div class="bareme-switch" role="tablist">
        <button type="button" class="btn-segment active" data-type="depot"
                data-bs-toggle="pill" data-bs-target="#pane-depot" role="tab" aria-selected="true">
            <span class="seg-ico">&#8595;</span> Dépôt
        </button>
        <button type="button" class="btn-segment" data-type="retrait"
                data-bs-toggle="pill" data-bs-target="#pane-retrait" role="tab" aria-selected="false">
            <span class="seg-ico">&#8593;</span> Retrait
        </button>
        <button type="button" class="btn-segment" data-type="transfert"
                data-bs-toggle="pill" data-bs-target="#pane-transfert" role="tab" aria-selected="false">
            <span class="seg-ico">&#8644;</span> Transfert
        </button>
    </div>

    <a href="<?= base_url('operateur/baremes/create') ?>" class="btn btn-mm-accent btn-sm">+ Ajouter un barème</a>
</div>

<div class="tab-content">

    <div class="tab-pane fade show active" id="pane-depot" role="tabpanel">
        <div class="mm-table-card">
            <div class="card-head">
                <h2><span class="badge-op depot me-2">&#8595;</span> Barème — Dépôt</h2>
                <span class="page-desc"><?= count($baremesDepot) ?> tranche(s)</span>
            </div>
            <?php renderBaremeRows($baremesDepot) ?>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-retrait" role="tabpanel">
        <div class="mm-table-card">
            <div class="card-head">
                <h2><span class="badge-op retrait me-2">&#8593;</span> Barème — Retrait</h2>
                <span class="page-desc"><?= count($baremesRetrait) ?> tranche(s)</span>
            </div>
            <?php renderBaremeRows($baremesRetrait) ?>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-transfert" role="tabpanel">
        <div class="mm-table-card">
            <div class="card-head">
                <h2><span class="badge-op transfert me-2">&#8644;</span> Barème — Transfert</h2>
                <span class="page-desc"><?= count($baremesTransfert) ?> tranche(s)</span>
            </div>
            <?php renderBaremeRows($baremesTransfert) ?>
        </div>
    </div>

</div>

<script>
// Bootstrap gère l'affichage des tab-pane automatiquement via data-bs-toggle="pill",
// on gère juste le style "active" du bouton segmenté nous-mêmes
document.querySelectorAll('.bareme-switch .btn-segment').forEach(function (btn) {
    btn.addEventListener('shown.bs.tab', function () {
        document.querySelectorAll('.bareme-switch .btn-segment').forEach(function (b) {
            b.classList.remove('active');
            b.setAttribute('aria-selected', 'false');
        });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');
    });
});
</script>

<?= $this->endSection() ?>