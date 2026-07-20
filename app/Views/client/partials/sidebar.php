<?php
// Page active pour surligner le lien du menu (à passer depuis le controller: $activePage = 'dashboard')
$activePage = $activePage ?? '';
?>
<aside class="mm-sidebar" id="mmSidebar">
    <div class="sidebar-brand">
        <div class="logo-mark">ML</div>
        <div>
            <div class="brand-name">MoneyLink</div>
            <div class="brand-tag">Espace client</div>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="u-name">Rakoto Jean</div>
        <div class="u-role">033 12 345 67</div>
    </div>

    <nav class="mm-nav">
        <a href="<?= base_url('client/dashboard') ?>" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-ico">&#9632;</span> Dashboard
        </a>
        <a href="<?= base_url('client/historique') ?>" class="nav-link <?= $activePage === 'historique' ? 'active' : '' ?>">
            <span class="nav-ico">&#8635;</span> Historique
        </a>
        <a href="<?= base_url('client/compte') ?>" class="nav-link <?= $activePage === 'compte' ? 'active' : '' ?>">
            <span class="nav-ico">&#9679;</span> Mon compte
        </a>
    </nav>

    <div class="sidebar-logout">
        <a href="<?= base_url('logout') ?>">&#10148; Déconnexion</a>
    </div>
</aside>
