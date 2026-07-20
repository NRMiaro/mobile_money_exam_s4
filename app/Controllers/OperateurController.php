<?php 
namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Services\PrefixeService;
use App\Services\TransactionService;
use App\Services\UtilisateurService;

class OperateurController extends BaseController{

    public function dashboard(){
        $transactionService = new TransactionService();
        $prefixeService = new PrefixeService();
        $utilisateurService = new UtilisateurService();
        $data =[
            'totalGains' => $transactionService->getTotalGains(),
            'totalPrefixes' => $prefixeService->getTotalPrefixes(),
            'nombreClientsActifs' => $utilisateurService->countClientsActifs()
        ];

        return view("operateur/dashboard",$data);
    }

}