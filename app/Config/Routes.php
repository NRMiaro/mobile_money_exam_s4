<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('login', 'Home::login');

// Client
$routes->get('client/dashboard', 'Home::clientDashboard');
$routes->get('client/depot', 'Home::clientDepot');
$routes->get('client/retrait', 'Home::clientRetrait');
$routes->get('client/transfert', 'Home::clientTransfert');
$routes->get('client/historique', 'Home::clientHistorique');
$routes->get('client/compte', 'Home::clientCompte');

// Opérateur
$routes->get('operateur/dashboard', 'Home::operateurDashboard');
$routes->get('operateur/prefixes', 'Home::operateurPrefixesIndex');
$routes->get('operateur/prefixes/create', 'Home::operateurPrefixesCreate');
$routes->get('operateur/baremes', 'Home::operateurBaremesIndex');
$routes->get('operateur/baremes/create', 'Home::operateurBaremesCreate');