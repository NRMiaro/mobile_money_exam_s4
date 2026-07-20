<?= $this->extend('client/layout') ?>
<?= $this->section('content') ?>

<?php
$labels = ['depot' => 'Dépôt', 'retrait' => 'Retrait', 'transfert' => 'Transfert'];

?>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="balance-ticket h-100">
            <div class="bt-chip"></div>
            <div class="bt-label">Solde disponible</div>
            <div class="bt-amount"><?= number_format($utilisateur['solde'], 0, ',', ' ') ?> Ar</div>
            <div class="bt-numero"><?= esc($utilisateur['numero']) ?></div>
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
        <h2>Dérnières transactions</h2>
    </div>

    <?php if (empty($transactions)): ?>
        <div class="p-4 text-center text-muted small">Aucune transaction pour le moment.</div>
    <?php else: ?>
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
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><?= esc($t['date']) ?></td>
                    <td><span class="badge-op <?= $t['type'] ?>"><?= $labels[$t['type']] ?></span></td>
                    <td class="fw-semibold"><?= $t['sens'] ?><?= number_format($t['montant'], 0, ',', ' ') ?> Ar</td>
                    <td><?= number_format($t['frais'], 0, ',', ' ') ?> Ar</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
