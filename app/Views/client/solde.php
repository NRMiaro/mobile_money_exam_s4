<h2>Mon solde</h2>

<p>
    Bonjour <?= esc($utilisateur['prenom']) ?>
    <?= esc($utilisateur['nom']) ?>
</p>

<h3>
    Solde : <?= number_format($utilisateur['solde'], 0, ',', ' ') ?> Ar
</h3>