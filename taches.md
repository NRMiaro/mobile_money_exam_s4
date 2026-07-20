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
Atao hoe Yas le tompon'le Systeme D'Information ohatra eto 
--- 

- [x] Creer une table operateur
- [x] Creer Model et Service (meme si y a rien encore dans Service)
  
- [x] Ajouter colonne prefixe[id_operateur]
- [x] mettre a jour jeu de donnees (atao ID = 1 YAS, de apetraka fotsiny ny ambiny)
- [x] mettre a jour fonction d'obtention de prefixe, pour filtrer si on veut prendre nos propres prefixes, ou celui d'un operateur particulier, ou tout
  
- [x] Creer table commission: #idOperateur, %age commission
- [x] Dans Transaction: 
  - [x] Ajouter colonne montant commission
  - [x] ajouter colonne idOperateurDestinataire 

- [ ] Mettre a jour page Situation gain - CaD afficher la somme des gains obtenus depuis des transactions :
  - [ ] Depot + retrait + transferts Yas2Yas
  - [ ] Transferts Yas2Autre
- [ ] Nouvelle page: montants a envoyer a chaque operateur
  - [ ] lister chaque operateur
  - [ ] determiner le montant (total commissions ngamba ty ?) pour chaque operateur
