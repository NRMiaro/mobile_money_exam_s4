<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'comptes';
$pageTitle = 'Comptes abonnés';
$pageDesc = 'Consultation des soldes des abonnés';
?>

<div class="mm-table-card">

    <div class="card-head">
        <div>
            <h2>Soldes des abonnés</h2>
            <small class="text-muted">
                Consultation des comptes enregistrés.
            </small>
        </div>
    </div>

    <?php if(empty($comptes)): ?>

        <div class="alert alert-warning">
            Aucun abonné enregistré.
        </div>

    <?php else: ?>

    <table class="table mm-table align-middle">

        <thead>
        <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Numéro</th>
            <th>Statut</th>
            <th class="text-end">Solde</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach($comptes as $c): ?>

        <tr>

            <td><?= $c['id'] ?></td>

            <td>
                <strong><?= esc($c['prenom'].' '.$c['nom']) ?></strong>
            </td>

            <td><?= esc($c['numero']) ?></td>

            <td>

                <?php if($c['is_actif']): ?>

                    <span class="badge bg-success">
                        Actif
                    </span>

                <?php else: ?>

                    <span class="badge bg-secondary">
                        Inactif
                    </span>

                <?php endif; ?>

            </td>

            <td class="text-end fw-bold">
                <?= number_format($c['solde'],0,',',' ') ?> Ar
            </td>

        </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>