<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'prefixes';
$pageTitle  = 'Préfixes';
$pageDesc   = 'Préfixes valables pour cet opérateur';
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <div>
            <h4 class="mb-0">Gestion des préfixes</h4>
            <small class="text-muted">
                Préfixes supportés par les différents opérateurs.
            </small>
        </div>

        <a href="<?= base_url('operateur/prefixes/create') ?>"
           class="btn btn-success">
            + Ajouter
        </a>

    </div>

    <div class="card-body">

        <h5 class="mb-3">
            Nos préfixes
        </h5>

        <?php if(empty($owner)): ?>

            <div class="alert alert-warning">
                Aucun préfixe configuré.
            </div>

        <?php else: ?>

            <div class="d-flex flex-wrap gap-2 mb-5">

                <?php foreach($owner as $p): ?>

                    <span class="badge rounded-pill bg-success fs-6 px-4 py-2">
                        <?= esc($p['prefixe']) ?>
                    </span>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <h5 class="mb-3">
            Préfixes des autres opérateurs
        </h5>

        <?php if(empty($others)): ?>

            <div class="text-muted">
                Aucun autre opérateur enregistré.
            </div>

        <?php else: ?>

            <div class="row g-4">

                <?php foreach($others as $operateur => $prefixes): ?>

                    <div class="col-lg-4 col-md-6">

                        <div class="card border-0 shadow-sm h-100">

                            <div class="card-header bg-light fw-semibold">
                                <?= esc($operateur) ?>
                            </div>

                            <div class="card-body">

                                <div class="d-flex flex-wrap gap-2">

                                    <?php foreach($prefixes as $p): ?>

                                        <span class="badge bg-light text-dark border px-3 py-2">
                                            <?= esc($p['prefixe']) ?>
                                        </span>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<?= $this->endSection() ?>