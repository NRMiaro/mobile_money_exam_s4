<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    // ---------- AUTH ----------
    public function login(): string
    {
        return view('auth/login');
    }

    // ---------- CLIENT ----------
    public function clientDashboard(): string
    {
        return view('client/dashboard');
    }

    public function clientDepot(): string
    {
        return view('client/depot');
    }

    public function clientRetrait(): string
    {
        return view('client/retrait');
    }

    public function clientTransfert(): string
    {
        return view('client/transfert');
    }

    public function clientHistorique(): string
    {
        return view('client/historique');
    }

    public function clientCompte(): string
    {
        return view('client/compte');
    }

    // ---------- OPERATEUR ----------
    public function operateurDashboard(): string
    {
        return view('operateur/dashboard');
    }

    public function operateurPrefixesIndex(): string
    {
        return view('operateur/prefixe/index');
    }

    public function operateurPrefixesCreate(): string
    {
        return view('operateur/prefixe/create');
    }

    public function operateurBaremesIndex(): string
    {
        return view('operateur/bareme/index');
    }

    public function operateurBaremesCreate(): string
    {
        return view('operateur/bareme/create');
    }
}