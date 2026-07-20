<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>
<div class="balance-card">

    <div class="balance-label">
        Solde disponible
    </div>

    <div class="balance-value">
        <?= number_format($utilisateur['solde'],0,',',' ') ?> Ar
        <?= session()->get('idUtilisateur')?> Ar
    </div>

</div>
<?= $this->endSection() ?>