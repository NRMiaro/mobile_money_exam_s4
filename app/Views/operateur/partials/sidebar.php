<?php
$activePage = $activePage ?? '';
?>
<aside class="mm-sidebar" id="mmSidebar">
    <div class="sidebar-brand">
        <div class="logo-mark">ML</div>
        <div>
            <div class="brand-name">MoneyLink</div>
            <div class="brand-tag">Back-office opérateur</div>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="u-name">Admin Système</div>
        <div class="u-role">Opérateur</div>
    </div>

    <nav class="mm-nav">
        <a href="<?= base_url('operateur/dashboard') ?>" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-ico">&#9632;</span> Dashboard
        </a>
        <a href="<?= base_url('operateur/prefixes') ?>" class="nav-link <?= $activePage === 'prefixes' ? 'active' : '' ?>">
            <span class="nav-ico">&#9741;</span> Préfixes
        </a>
        <a href="<?= base_url('operateur/baremes') ?>" class="nav-link <?= $activePage === 'baremes' ? 'active' : '' ?>">
            <span class="nav-ico">&#8801;</span> Barèmes
        </a>
        <a href="<?= base_url('operateur/comptes') ?>"
            class="nav-link <?= $activePage === 'comptes' ? 'active' : '' ?>">
            <span class="nav-ico">&#128179;</span>
            Comptes abonnés
        </a>
    </nav>

    <div class="sidebar-logout">
        <a href="<?= base_url('logout') ?>">&#10148; Déconnexion</a>
    </div>
</aside>