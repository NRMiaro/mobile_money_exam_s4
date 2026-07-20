<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<?php
$activePage = 'prefixes';
$pageTitle  = 'Ajouter un préfixe';
$pageDesc   = 'Nouveau préfixe valable pour l\'opérateur';
?>

<div class="op-form-card">
    <div class="op-form-head head-transfert">
        <div class="op-ico">&#9741;</div>
        <div>
            <div class="op-title">Nouveau préfixe</div>
            <div class="op-sub">Ex : 034, 038...</div>
        </div>
    </div>

    <div class="op-form-body">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('operateur/prefixes/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="id_operateur" class="form-label">
                    Opérateur
                </label>

                <select
                    class="form-select"
                    id="id_operateur"
                    name="id_operateur"
                    required>

                    <option value="">-- Choisir un opérateur --</option>

                    <?php foreach ($operateurs as $operateur): ?>

                        <option
                            value="<?= $operateur['id'] ?>"
                            <?= old('id_operateur') == $operateur['id'] ? 'selected' : '' ?>>

                            <?= esc($operateur['libelle']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <div class="mb-4">
                <label for="prefixe" class="form-label">Préfixe</label>
                <input type="text" class="form-control" id="prefixe" name="prefixe"
                    value="<?= old('prefixe') ?>"
                    placeholder="034" maxlength="3" pattern="[0-9]{3}" required>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('operateur/prefixes') ?>" class="btn btn-outline-secondary flex-fill">Annuler</a>
                <button type="submit" class="btn btn-mm-primary flex-fill">Valider</button>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>