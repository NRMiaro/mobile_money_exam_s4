<?php 
namespace App\Services;

use App\Models\UtilisateurModel;

class UtilisateurService{

    public function countClientsActifs(): int
    {   
        $utilisateurModel = new UtilisateurModel();
        return $utilisateurModel->where('is_admin', 0)
            ->where('is_actif', 1)
            ->countAllResults();
    }
}