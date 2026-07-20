-- Active: 1784552473363@@127.0.0.1@3306
-- Clients supplémentaires (Orange et Airtel) pour pouvoir tester les transferts externes
INSERT INTO utilisateur
    (nom, prenom, numero, date_naissance, code_secret, solde, is_admin)
VALUES
    ('Randria', 'Tiana', '0321112222', '1995-03-14', '3333', 100000, 0), -- Orange
    ('Rasoa', 'Faly', '0371113333', '1997-07-22', '4444', 80000, 0),     -- Orange
    ('Andria', 'Njaka', '0331114444', '1996-09-05', '5555', 120000, 0), -- Airtel
    ('Rakotoson', 'Voahangy', '0332225555', '1994-12-30', '6666', 90000, 0); -- Airtel

-- ============================================
-- TRANSACTIONS DE TEST
-- Rappel des id_utilisateur (selon ordre d'insertion) :
-- 1 = Admin (034...)
-- 2 = Rakoto Jean   (0341234567) - Yas
-- 3 = Rabe Marie    (0387654321) - Yas
-- 4 = Randria Tiana (0321112222) - Orange
-- 5 = Rasoa Faly    (0371113333) - Orange
-- 6 = Andria Njaka  (0331114444) - Airtel
-- 7 = Rakotoson Voahangy (0332225555) - Airtel
-- ============================================

-- ==================== DEPOT ====================
-- Depot gratuit (tranche 0-9999)
INSERT INTO transactions
    (id_type_transaction, id_client_source, id_client_destinataire, id_operateur_destinataire, montant, frais, montant_commission)
VALUES
    (1, NULL, 2, NULL, 5000, 0, 0),
    (1, NULL, 3, NULL, 30000, 0, 0),
    (1, NULL, 4, NULL, 80000, 1000, 0),
    (1, NULL, 6, NULL, 150000, 1000, 0);

-- ==================== RETRAIT ====================
INSERT INTO transactions
    (id_type_transaction, id_client_source, id_client_destinataire, id_operateur_destinataire, montant, frais, montant_commission)
VALUES
-- tranche 0-9999 => frais 300
    (2, 2, NULL, NULL, 5000, 300, 0),
-- tranche 10000-19999 => frais 500
    (2, 3, NULL, NULL, 15000, 500, 0),
-- tranche 20000-49999 => frais 1000
    (2, 4, NULL, NULL, 25000, 1000, 0),
-- tranche 50000-99999 => frais 1500
    (2, 6, NULL, NULL, 70000, 1500, 0);

-- ==================== TRANSFERT Yas -> Yas (interne, pas de commission) ====================
INSERT INTO transactions
    (id_type_transaction, id_client_source, id_client_destinataire, id_operateur_destinataire, montant, frais, montant_commission)
VALUES
-- tranche 20000-49999 => frais 600
    (3, 2, 3, 1, 20000, 600, 0),
-- tranche 0-9999 => frais 200
    (3, 3, 2, 1, 5000, 200, 0);

-- ==================== TRANSFERT Yas -> Orange (externe, commission 1.5%) ====================
INSERT INTO transactions
    (id_type_transaction, id_client_source, id_client_destinataire, id_operateur_destinataire, montant, frais, montant_commission)
VALUES
-- montant 50000 => frais (50000-99999) 1000, commission 50000*1.5% = 750
    (3, 2, 4, 2, 50000, 1000, 750),
-- montant 15000 => frais (10000-19999) 400, commission 15000*1.5% = 225
    (3, 3, 5, 2, 15000, 400, 225);

-- ==================== TRANSFERT Yas -> Airtel (externe, commission 2%) ====================
INSERT INTO transactions
    (id_type_transaction, id_client_source, id_client_destinataire, id_operateur_destinataire, montant, frais, montant_commission)
VALUES
-- montant 50000 => frais 1000, commission 50000*2% = 1000
    (3, 2, 6, 3, 50000, 1000, 1000),
-- montant 8000 => frais (0-9999) 200, commission 8000*2% = 160
    (3, 3, 7, 3, 8000, 200, 160);

-- ==================== TRANSFERT Orange -> Orange (reste "externe" côté Yas, commission s'applique quand même) ====================
INSERT INTO transactions
    (id_type_transaction, id_client_source, id_client_destinataire, id_operateur_destinataire, montant, frais, montant_commission)
VALUES
-- montant 30000 => frais (20000-49999) 600, commission 30000*1.5% = 450
    (3, 4, 5, 2, 30000, 600, 450);

-- ==================== TRANSFERT Airtel -> Airtel ====================
INSERT INTO transactions
    (id_type_transaction, id_client_source, id_client_destinataire, id_operateur_destinataire, montant, frais, montant_commission)
VALUES
-- montant 12000 => frais (10000-19999) 400, commission 12000*2% = 240
    (3, 6, 7, 3, 12000, 400, 240);