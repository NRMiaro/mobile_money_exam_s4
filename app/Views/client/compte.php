<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'compte';
$pageTitle  = 'Mon compte';
$pageDesc   = 'Vos informations personnelles';

// Données en dur pour l'intégration
$client = [
    'nom'            => 'Rakoto',
    'prenom'         => 'Jean',
    'numero'         => '033 12 345 67',
    'date_naissance' => '14/03/1999',
    'solde'          => 125400,
    'created_at'     => '02/06/2026',
    'is_actif'       => true,
];
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="balance-ticket">
            <div class="bt-chip"></div>
            <div class="bt-label">Solde disponible</div>
            <div class="bt-amount"><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</div>
            <div class="bt-numero"><?= esc($client['numero']) ?></div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="mm-card p-4">
            <div class="eyebrow mb-2">Informations personnelles</div>

            <div class="profile-row">
                <span class="p-label">Nom complet</span>
                <span class="p-value"><?= esc($client['prenom']) ?> <?= esc($client['nom']) ?></span>
            </div>
            <div class="profile-row">
                <span class="p-label">Numéro</span>
                <span class="p-value"><?= esc($client['numero']) ?></span>
            </div>
            <div class="profile-row">
                <span class="p-label">Date de naissance</span>
                <span class="p-value"><?= esc($client['date_naissance']) ?></span>
            </div>
            <div class="profile-row">
                <span class="p-label">Client depuis</span>
                <span class="p-value"><?= esc($client['created_at']) ?></span>
            </div>
            <div class="profile-row">
                <span class="p-label">Statut du compte</span>
                <span class="p-value">
                    <?php if ($client['is_actif']): ?>
                        <span class="badge-op depot">Actif</span>
                    <?php else: ?>
                        <span class="badge-op retrait">Désactivé</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
