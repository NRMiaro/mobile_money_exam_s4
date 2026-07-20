<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
// Données en dur pour l'intégration - à remplacer par $data venant du controller
$activePage = 'dashboard';
$pageTitle  = 'Dashboard';
$pageDesc   = 'Bienvenue, gérez vos opérations mobile money';
$solde      = 125400;
$numero     = '033 12 345 67';
?>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="balance-ticket h-100">
            <div class="bt-chip"></div>
            <div class="bt-label">Solde disponible</div>
            <div class="bt-amount"><?= number_format($solde, 0, ',', ' ') ?> Ar</div>
            <div class="bt-numero"><?= esc($numero) ?></div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="row g-3 h-100">
            <div class="col-md-4">
                <a href="<?= base_url('client/depot') ?>" class="action-card">
                    <div class="action-ico depot">&#8595;</div>
                    <div>
                        <div class="action-title">Dépôt</div>
                        <div class="action-sub">Créditer mon compte</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= base_url('client/retrait') ?>" class="action-card">
                    <div class="action-ico retrait">&#8593;</div>
                    <div>
                        <div class="action-title">Retrait</div>
                        <div class="action-sub">Retirer des fonds</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= base_url('client/transfert') ?>" class="action-card">
                    <div class="action-ico transfert">&#8644;</div>
                    <div>
                        <div class="action-title">Transfert</div>
                        <div class="action-sub">Envoyer à un contact</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="mm-table-card">
    <div class="card-head">
        <h2>Dernières transactions</h2>
        <a href="<?= base_url('client/historique') ?>" class="small fw-semibold" style="color: var(--mm-primary)">Voir tout &rarr;</a>
    </div>
    <table class="table mm-table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Frais</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>19/07/2026 14:32</td>
            <td><span class="badge-op depot">Dépôt</span></td>
            <td class="fw-semibold">+50 000 Ar</td>
            <td>0 Ar</td>
        </tr>
        <tr>
            <td>18/07/2026 09:10</td>
            <td><span class="badge-op transfert">Transfert</span></td>
            <td class="fw-semibold">-15 000 Ar</td>
            <td>300 Ar</td>
        </tr>
        <tr>
            <td>16/07/2026 18:45</td>
            <td><span class="badge-op retrait">Retrait</span></td>
            <td class="fw-semibold">-20 000 Ar</td>
            <td>500 Ar</td>
        </tr>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
