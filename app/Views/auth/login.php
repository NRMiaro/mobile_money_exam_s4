<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — MoneyLink</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-brand">
            <div class="logo-mark">ML</div>
            <div>
                <div class="brand-name">MoneyLink</div>
                <div class="brand-tag">Mobile Money</div>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="numero" class="form-label">Numéro de téléphone</label>
                <input type="text" class="form-control" id="numero" name="numero"
                       placeholder="033 12 345 67" required autofocus>
            </div>

            <div class="mb-4">
                <label for="code_secret" class="form-label">Code secret (PIN)</label>
                <input type="password" class="form-control pin-input" id="code_secret" name="code_secret"
                       maxlength="4" inputmode="numeric" pattern="[0-9]{4}" placeholder="••••" required>
            </div>

            <button type="submit" class="btn btn-mm-primary w-100">Se connecter</button>
        </form>

    </div>
</div>

<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
