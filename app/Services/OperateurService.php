<?php namespace App\Services ;

use App\Models\OperateurModel;

class OperateurService {
    private OperateurModel $model;

    public function __construct()
    {
        $this->model = new OperateurModel();
    }

    public function getAll(){
        return $this->model->orderBy('libelle', 'ASC')
            ->findAll();
    }

}