-- Active: 1784550825520@@127.0.0.1@3306
PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS transactions;

DROP TABLE IF EXISTS commission;

DROP TABLE IF EXISTS bareme;

DROP TABLE IF EXISTS type_transaction;

DROP TABLE IF EXISTS prefixe;

DROP TABLE IF EXISTS utilisateur;

DROP TABLE IF EXISTS operateur;
DROP TABLE IF EXISTS choix_epargne;

DROP TABLE IF EXISTS promotion;



PRAGMA foreign_keys = ON;

CREATE TABLE operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL UNIQUE
);

CREATE TABLE utilisateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    numero TEXT NOT NULL UNIQUE,
    date_naissance DATE NOT NULL,
    code_secret TEXT NOT NULL,
    solde REAL NOT NULL DEFAULT 0,
    solde_epargne REAL NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_actif INTEGER NOT NULL DEFAULT 1,
    is_admin INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE prefixe (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    id_operateur INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_operateur) REFERENCES operateur (id)
);

CREATE TABLE type_transaction (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL UNIQUE
);

CREATE TABLE bareme (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_transaction INTEGER NOT NULL,
    montant_min REAL NOT NULL CHECK (montant_min >= 0),
    montant_max REAL NOT NULL CHECK (montant_max > montant_min),
    frais REAL NOT NULL CHECK (frais >= 0),
    FOREIGN KEY (id_type_transaction) REFERENCES type_transaction (id)
);

CREATE TABLE commission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur INTEGER NOT NULL,
    pct_commission REAL NOT NULL CHECK (
        pct_commission >= 0
        AND pct_commission <= 100
    ),
    FOREIGN KEY (id_operateur) REFERENCES operateur (id)
);

CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_transaction INTEGER NOT NULL,
    id_client_source INTEGER,
    id_client_destinataire INTEGER,
    id_operateur_destinataire INTEGER,
    montant REAL NOT NULL CHECK (montant > 0),
    frais REAL NOT NULL CHECK (frais >= 0),
    montant_commission REAL NOT NULL DEFAULT 0,
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_type_transaction) REFERENCES type_transaction (id),
    FOREIGN KEY (id_client_source) REFERENCES utilisateur (id),
    FOREIGN KEY (id_client_destinataire) REFERENCES utilisateur (id),
    FOREIGN KEY (id_operateur_destinataire) REFERENCES operateur (id)
);

INSERT INTO
    operateur
VALUES (1, 'Yas'),
    (2, 'Orange'),
    (3, 'Airtel');

-- jeu de donnees tsotsotra

INSERT INTO
    utilisateur (
        nom,
        prenom,
        numero,
        date_naissance,
        code_secret,
        solde,
        is_admin
    )
VALUES (
        'Admin',
        'System',
        '0340000000',
        '1990-01-01',
        '1234',
        0,
        1
    ),
    (
        'Rakoto',
        'Jean',
        '0341234567',
        '1998-05-12',
        '1111',
        250000,
        0
    ),
    (
        'Rabe',
        'Marie',
        '0387654321',
        '1999-11-20',
        '2222',
        200000,
        0
    ),
    (
        'Razoky',
        'Be',
        '0323232232',
        '1999-11-20',
        '3232',
        200000,
        0
    ),
    (
        'Paul',
        'Ine',
        '0333333333',
        '1999-11-20',
        '2222',
        200000,
        0
    );

-- Préfixes
INSERT INTO
    prefixe (prefixe, id_operateur)
VALUES ('034', 1),
    ('038', 1),
    ('032', 2),
    ('037', 2),
    ('033', 3);

-- Types de transaction
INSERT INTO
    type_transaction (id, libelle)
VALUES (1, 'DEPOT'),
    (2, 'RETRAIT'),
    (3, 'TRANSFERT');

-- Barèmes
-- id_type_transaction :
-- 1 = DEPOT
-- 2 = RETRAIT
-- 3 = TRANSFERT

INSERT INTO
    bareme (
        id_type_transaction,
        montant_min,
        montant_max,
        frais
    )
VALUES
    -- DEPOT (gratuit)
    (1, 0, 9999, 0),
    (1, 10000, 49999, 0),
    (1, 50000, 999999, 1000),
    -- RETRAIT
    (2, 0, 9999, 300),
    (2, 10000, 19999, 500),
    (2, 20000, 49999, 1000),
    (2, 50000, 99999, 1500),
    -- TRANSFERT
    (3, 0, 9999, 200),
    (3, 10000, 19999, 400),
    (3, 20000, 49999, 600),
    (3, 50000, 99999, 1000);

-- Commissions
INSERT INTO
    commission (id_operateur, pct_commission)
VALUES (2, 1.5), -- Orange
    (3, 2.0);

CREATE TABLE promotion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pourcentage REAL NOT NULL CHECK (
        pourcentage >= 0
        AND pourcentage <= 100
    ),
    is_actif INTEGER NOT NULL DEFAULT 1
);

INSERT INTO promotion (id, pourcentage) VALUES (1, 10);

-- Table choix_epargne

CREATE TABLE choix_epargne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_client INTEGER UNIQUE NOT NULL,
    pourcentage REAL NOT NULL CHECK (
        pourcentage >= 0
        AND pourcentage <= 100
    )
);

INSERT INTO choix_epargne (id_client, pourcentage)
VALUES 
(2, 40.0);