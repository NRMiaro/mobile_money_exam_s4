<?php
$activePage = $activePage ?? '';

$nom = session()->get('nom') ?? 'Utilisateur';
$numero = session()->get('numero') ?? '';
?>

<aside class="mm-sidebar">

    <div class="sidebar-brand">
        <div class="logo-mark">ML</div>
        <div>
            <div class="brand-name">MoneyLink</div>
            <div class="brand-tag">Mobile Money</div>
        </div>
    </div>

    <div class="sidebar-user">

        <div>
            <div class="u-name"><?= esc($nom) ?></div>
            <div class="u-role"><?= esc($numero) ?></div>
        </div>
    </div>

    <nav class="mm-nav">

        <a href="<?= base_url('client/dashboard') ?>"
            class="nav-link <?= $activePage == 'dashboard' ? 'active' : '' ?>">
            Tableau de bord
        </a>

        <!-- <a href="<?= base_url('client/solde') ?>"
            class="nav-link <?= $activePage == 'solde' ? 'active' : '' ?>">
            Mon solde
        </a> -->

        <a href="<?= base_url('client/depot') ?>"
            class="nav-link <?= $activePage == 'depot' ? 'active' : '' ?>">
            Dépôt
        </a>

        <a href="<?= base_url('client/retrait') ?>"
            class="nav-link <?= $activePage == 'retrait' ? 'active' : '' ?>">
            Retrait
        </a>

        <a href="<?= base_url('client/transfert') ?>"
            class="nav-link <?= $activePage == 'transfert' ? 'active' : '' ?>">
            Transfert
        </a>

        <a href="<?= base_url('client/historique') ?>"
            class="nav-link <?= $activePage == 'historique' ? 'active' : '' ?>">
            Historique
        </a>

        <a href="<?= base_url('client/transfert-multiple') ?>"
            class="nav-link">
            Transfert multiple
        </a>

    </nav>

    <div class="sidebar-logout">
        <a href="<?= base_url('logout') ?>">
            Déconnexion
        </a>
    </div>

</aside>