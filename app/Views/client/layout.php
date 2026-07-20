<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MoneyLink' ?> — Espace client</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="app-shell">

    <?= $this->include('client/partials/sidebar') ?>

    <div class="mm-main">

        <header class="mm-topbar">
            <div>
                <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
                <?php if (isset($pageDesc)): ?>
                    <div class="page-desc"><?= $pageDesc ?></div>
                <?php endif; ?>
            </div>
        </header>

        <main class="mm-content">
            <?= $this->renderSection('content') ?>
        </main>

        <?= $this->include('client/partials/footer') ?>

    </div>

</div>

<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
