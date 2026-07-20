<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('login', 'Home::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Client
$routes->group('/client', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Home::clientDashboard');
    $routes->get('depot', 'Home::clientDepot');
    $routes->get('retrait', 'Home::clientRetrait');
    $routes->get('transfert', 'Home::clientTransfert');
    $routes->get('historique', 'Home::clientHistorique');
    $routes->get('compte', 'Home::clientCompte');
});

// Opérateur
$routes->group('/operateur', ['filter' => 'auth'], function($routes){
    $routes->get('', 'Home::operateurDashboard');
    $routes->get('dashboard', 'Home::operateurDashboard');
    $routes->get('prefixes', 'Home::operateurPrefixesIndex');
    $routes->get('prefixes/create', 'Home::operateurPrefixesCreate');
    $routes->get('baremes', 'Home::operateurBaremesIndex');
    $routes->get('baremes/create', 'Home::operateurBaremesCreate');
});
