<?php
$activePage = $activePage ?? '';

$nom = session()->get('nom') ?? 'Utilisateur';
$numero = session()->get('numero') ?? '';
?>

<!-- Bouton hamburger (visible uniquement quand l'écran devient petit) -->
<button class="btn mm-sidebar-toggle" type="button" id="mmSidebarToggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mmSidebar">
    <span class="hamburger-bar"></span>
    <span class="hamburger-bar"></span>
    <span class="hamburger-bar"></span>
</button>

<!-- Overlay pour fermer la sidebar en cliquant à côté -->
<div class="mm-sidebar-overlay" id="mmSidebarOverlay"></div>

<aside class="mm-sidebar" id="mmSidebar">

    <div class="sidebar-brand">
        <div class="logo-mark">ML</div>
        <div>
            <div class="brand-name">MoneyLink</div>
            <div class="brand-tag">Mobile Money</div>
        </div>
        <button class="btn-close btn-close-white ms-auto" id="mmSidebarClose" aria-label="Fermer"></button>
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
            <span class="nav-ico">&#9632;</span> Tableau de bord
        </a>

        <!-- <a href="<?= base_url('client/solde') ?>"
            class="nav-link <?= $activePage == 'solde' ? 'active' : '' ?>">
            <span class="nav-ico">&#164;</span> Mon solde
        </a> -->

        <a href="<?= base_url('client/depot') ?>"
            class="nav-link <?= $activePage == 'depot' ? 'active' : '' ?>">
            <span class="nav-ico">&#8595;</span> Dépôt
        </a>

        <a href="<?= base_url('client/retrait') ?>"
            class="nav-link <?= $activePage == 'retrait' ? 'active' : '' ?>">
            <span class="nav-ico">&#8593;</span> Retrait
        </a>

        <a href="<?= base_url('client/transfert') ?>"
            class="nav-link <?= $activePage == 'transfert' ? 'active' : '' ?>">
            <span class="nav-ico">&#8644;</span> Transfert
        </a>

        <a href="<?= base_url('client/historique') ?>"
            class="nav-link <?= $activePage == 'historique' ? 'active' : '' ?>">
            <span class="nav-ico">&#8801;</span> Historique
        </a>

        <a href="<?= base_url('client/transfert-multiple') ?>"
            class="nav-link <?= $activePage == 'transfert-multiple' ? 'active' : '' ?>">
            <span class="nav-ico">&#9776;</span> Transfert multiple
        </a>

    </nav>

    <div class="sidebar-logout">
        <a href="<?= base_url('logout') ?>">
            &#10148; Déconnexion
        </a>
    </div>

</aside>

<script>
(function () {
    const sidebar = document.getElementById('mmSidebar');
    const toggleBtn = document.getElementById('mmSidebarToggle');
    const closeBtn = document.getElementById('mmSidebarClose');
    const overlay = document.getElementById('mmSidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
        toggleBtn.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        toggleBtn.setAttribute('aria-expanded', 'false');
    }

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) closeSidebar();
    });

    sidebar.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 900) closeSidebar();
        });
    });
})();
</script>