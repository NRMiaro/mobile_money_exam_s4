<?php

namespace App\Services;

use App\Models\UtilisateurModel;

class UtilisateurService
{
    private UtilisateurModel $model ;
    public function __construct()
    {
        $this->model = new UtilisateurModel();
    }

    public function countClientsActifs(): int
    {
        return $this->model->where('is_admin', 0)
            ->where('is_actif', 1)
            ->countAllResults();
    }

    public function getComptesAbonnes(): array
    {
        return $this->model
            ->where('is_admin', 0)
            ->orderBy('solde', 'DESC')
            ->findAll();
    }

    public function getById($id){
        return $this->model
            ->find($id);
    }
}
