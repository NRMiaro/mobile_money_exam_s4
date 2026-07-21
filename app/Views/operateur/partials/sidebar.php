<?php
$activePage = $activePage ?? '';
?>

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
            <div class="brand-tag">Back-office opérateur</div>
        </div>
        <!-- Bouton fermer (visible uniquement en mode tiroir mobile) -->
        <button class="btn-close btn-close-white ms-auto d-none" id="mmSidebarClose" aria-label="Fermer" style="display:none;"></button>
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
            <span class="nav-ico">&#9679;</span>
            Comptes abonnés
        </a>
        <a href="<?= base_url('operateur/commissions') ?>"
            class="nav-link <?= $activePage === 'commissions' ? 'active' : '' ?>">
            <span class="nav-ico">&#8644;</span>
            Situation commission
        </a>
    </nav>

    <div class="sidebar-logout">
        <a href="<?= base_url('logout') ?>">&#10148; Déconnexion</a>
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

    // Ferme automatiquement la sidebar si on repasse en desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeSidebar();
        }
    });

    // Ferme la sidebar après un clic sur un lien (mobile), pratique en navigation
    sidebar.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 900) closeSidebar();
        });
    });
})();
</script>