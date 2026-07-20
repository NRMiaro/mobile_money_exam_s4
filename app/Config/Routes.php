<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('login', 'AuthController::showLogin');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Client
$routes->group('/client', ['filter' => ['auth']], function($routes) {
    $routes->get('', 'Home::clientDashboard');
    $routes->get('dashboard', 'Home::clientDashboard');
    $routes->get('depot', 'Home::clientDepot');
    $routes->get('retrait', 'Home::clientRetrait');
    $routes->get('transfert', 'Home::clientTransfert');
    $routes->get('historique', 'Home::clientHistorique');
    $routes->get('compte', 'Home::clientCompte');
});

// Opérateur
$routes->group('/operateur', ['filter' => ['admin']], function($routes){
    $routes->get('', 'OperateurController::dashboard');
    $routes->get('dashboard', 'OperateurController::dashboard');
    $routes->get('prefixes', 'OperateurController::operateurPrefixesIndex');
    $routes->get('prefixes/create', 'OperateurController::operateurPrefixesCreate');
    $routes->get('baremes', 'OperateurController::operateurBaremesIndex');
    $routes->get('baremes/create', 'OperateurController::operateurBaremesCreate');
});
