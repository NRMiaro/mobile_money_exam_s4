<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Services\BaremeService;
use App\Services\PrefixeService;
use App\Services\TransactionService;
use App\Services\UtilisateurService;

class OperateurController extends BaseController
{

    private BaremeService $baremeService;

    public function __construct()
    {
        $this->baremeService = new BaremeService();
    }

    public function dashboard()
    {
        $transactionService = new TransactionService();
        $prefixeService = new PrefixeService();
        $utilisateurService = new UtilisateurService();
        $data = [
            'totalGains' => $transactionService->getTotalGains(),
            'totalPrefixes' => $prefixeService->getTotalPrefixes(),
            'nombreClientsActifs' => $utilisateurService->countClientsActifs()
        ];

        return view("operateur/dashboard", $data);
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
        return view('operateur/bareme/index', [
            'baremesDepot' => $this->baremeService->getBaremesDepot(),
            'baremesRetrait' => [],
            'baremesTransfert' => [],
        ]);
    }

    public function operateurBaremesCreate(): string
    {
        return view('operateur/bareme/create');
    }
}
