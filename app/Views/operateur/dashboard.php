<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'dashboard';
$pageTitle  = 'Dashboard';
$pageDesc   = 'Vue d\'ensemble de la plateforme';

// Données en dur pour l'intégration
$gainTotal    = 2450300;
$nbPrefixes   = 3;
$nbComptes    = 128;
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-ico">&#8721;</div>
            <div>
                <div class="stat-value"><?= number_format($gainTotal, 0, ',', ' ') ?> Ar</div>
                <div class="stat-label">Gain total (frais)</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-ico">&#9741;</div>
            <div>
                <div class="stat-value"><?= $nbPrefixes ?></div>
                <div class="stat-label">Préfixes configurés</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-ico">&#9679;</div>
            <div>
                <div class="stat-value"><?= $nbComptes ?></div>
                <div class="stat-label">Comptes clients</div>
            </div>
        </div>
    </div>
</div>

<div class="eyebrow mb-2">Actions rapides</div>
<div class="row g-3">
    <div class="col-md-6">
        <a href="<?= base_url('operateur/prefixes') ?>" class="action-card">
            <div class="action-ico config">&#9741;</div>
            <div>
                <div class="action-title">Configuration des préfixes</div>
                <div class="action-sub">Gérer les préfixes valables de l'opérateur</div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="<?= base_url('operateur/baremes') ?>" class="action-card">
            <div class="action-ico config">&#8801;</div>
            <div>
                <div class="action-title">Barèmes de transaction</div>
                <div class="action-sub">CRUD des frais par tranche de montant</div>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
