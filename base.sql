-- Active: 1784547053848@@127.0.0.1@3306
PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS bareme;
DROP TABLE IF EXISTS type_transaction;
DROP TABLE IF EXISTS prefixe;
DROP TABLE IF EXISTS utilisateur;

PRAGMA foreign_keys = ON;

CREATE TABLE utilisateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    numero TEXT NOT NULL UNIQUE,
    date_naissance DATE NOT NULL,
    code_secret TEXT NOT NULL,
    solde REAL NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_actif INTEGER NOT NULL DEFAULT 1,
    is_admin INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE prefixe (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE type_transaction (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL UNIQUE
);

CREATE TABLE bareme (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_transaction INTEGER NOT NULL,
    montant_min REAL NOT NULL CHECK(montant_min >= 0),
    montant_max REAL NOT NULL CHECK(montant_max > montant_min),
    frais REAL NOT NULL CHECK(frais >= 0),

    FOREIGN KEY(id_type_transaction)
        REFERENCES type_transaction(id)
);

CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_transaction INTEGER NOT NULL,
    id_client_source INTEGER,
    id_client_destinataire INTEGER,
    montant REAL NOT NULL CHECK(montant > 0),
    frais REAL NOT NULL CHECK(frais >= 0),
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_type_transaction)
        REFERENCES type_transaction(id),

    FOREIGN KEY(id_client_source)
        REFERENCES utilisateur(id),

    FOREIGN KEY(id_client_destinataire)
        REFERENCES utilisateur(id)
);


-- jeu de donnees tsotsotra 

INSERT INTO utilisateur
    (nom, prenom, numero, date_naissance, code_secret, solde, is_admin)
VALUES
    ('Admin', 'System', '0340000000', '1990-01-01', '1234', 0, 1),
    ('Rakoto', 'Jean', '0341234567', '1998-05-12', '1111', 250000, 0),
    ('Rabe', 'Marie', '0387654321', '1999-11-20', '2222', 200000, 0);

-- Préfixes
INSERT INTO prefixe (prefixe)
VALUES
('034'),
('038');

-- Types de transaction
INSERT INTO type_transaction (id, libelle)
VALUES
(1, 'DEPOT'),
(2, 'RETRAIT'),
(3, 'TRANSFERT');

-- Barèmes
-- id_type_transaction :
-- 1 = DEPOT
-- 2 = RETRAIT
-- 3 = TRANSFERT

INSERT INTO bareme
    (id_type_transaction, montant_min, montant_max, frais)
VALUES
    -- DEPOT (gratuit)
    (1, 0, 50000, 0),
    (1, 50000, 500000, 0),
    (1, 500000, 10000000, 0),
    -- RETRAIT
    (2, 0, 50000, 500),
    (2, 50000, 100000, 1000),
    (2, 100000, 500000, 2000),
    (2, 500000, 10000000, 5000),
    -- TRANSFERT
    (3, 0, 50000, 300),
    (3, 50000, 100000, 600),
    (3, 100000, 500000, 1200),
    (3, 500000, 10000000, 3000);
