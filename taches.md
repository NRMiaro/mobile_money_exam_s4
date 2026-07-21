# Tâches - Mobile Money S4
## Version 1
### Côté opérateur
#### Gestion des préfixes (Ryan)
- [x] Ajout des préfixes (033, 034, 037, 038...)
- [x] Liste des prefixes

#### Types d'opérations
- [x] CRUD des types d'opérations
  - [x] Dépôt
  - [x] Retrait
  - [x] Transfert

#### Barèmes des frais (Miaro)
- [ ] Ajout des barèmes
- [x] Modification des barèmes: lien avec les deux baremes intervalle
- [x] Définition des tranches de montant

#### Tableau de bord opérateur (Ryan)
- [x] Situation des gains (frais de retrait + transfert)
- [ ] Situation des comptes clients

---

### Côté client

#### Authentification
- [x] Connexion avec numéro de téléphone
- [x] Vérification du code secret

---

### Opérations (Miaro)

#### Solde
- [x] Consulter le solde

#### Dépôt
- [x] Effectuer un dépôt
- [x] Application automatique du barème
- [x] Enregistrement de la transaction

#### Retrait
- [x] Effectuer un retrait
- [x] Vérification du solde
- [x] Application des frais
- [x] Enregistrement de la transaction

#### Transfert
- [x] Transférer vers un autre numéro
- [x] Vérification de l'existence du destinataire
- [x] Vérification du solde
- [x] Débit de l'expéditeur
- [x] Crédit du destinataire
- [x] Enregistrement de la transaction

#### Historique des transactions (Ryan)
- [x] Afficher l'historique des opérations

---

### Interface

#### Template (Ryan)
- [x] Intégration du template

#### Client
- [x] Dashboard
- [x] Sidebar
- [x] Page Solde
- [x] Page Dépôt
- [x] Page Retrait
- [x] Page Transfert
- [ ] Page Historique améliorée
---


****

## Version 2

Atao hoe Yas le tompon'ny Systeme D'Information, ohatra eto

---

### Mise en place multi-opérateurs (Miaro)

- [x] Créer une table opérateur
- [x] Créer Model et Service (même si y a rien encore dans Service)
- [x] Ajouter colonne prefixe[id_operateur]
- [x] Mettre à jour jeu de données (ID = 1 pour Yas, arranger le reste)
- [x] Mettre à jour fonction d'obtention de préfixe, pour filtrer si on veut prendre nos propres préfixes, ou celui d'un opérateur particulier, ou tout

---

### Commissions et gains (Ryan)

#### Table commission
- [x] Créer table commission : #idOperateur, %age commission

#### Table transaction
- [x] Ajouter colonne montant commission
- [x] Ajouter colonne idOperateurDestinataire

#### Affichage
- [x] Affichage dynamique frais quand client veut effectuer une transaction

#### Page Situation gain
- [x] Mettre à jour page Situation gain, c'est-à-dire afficher la somme des gains obtenus depuis des transactions :
  - [x] Dépôt + retrait + transferts Yas2Yas
  - [x] Transferts Yas2Autre

#### Nouvelle page : montants à envoyer à chaque opérateur
- [x] Lister chaque opérateur
- [x] Déterminer le montant (total commissions) pour chaque opérateur

---

### Transfert — frais et commission (Miaro)

- [x] Client qui veut faire transfert : proposer d'envoyer le frais de retrait du destinataire
  - [x] Obtenir liste baremesRetraits dans la page transfert
  - [x] Dynamique onChange montant (afficher frais déjà fait) : checkbox "Inclure des frais de retraits XXXX Ar"
  - [x] Dynamique si Yas2Autre : afficher commission prélevée
  - [x] Dynamique : ajouter commission si Yas2Autre

---