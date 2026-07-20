<?php namespace App\Services;

use App\Models\BaremeModel;
use App\Models\TypeTransactionModel;

class BaremeService {
    
    private BaremeModel $model;

    public function __construct()
    {
        $this->model = new BaremeModel();
    }


    public function getBaremesDepot(){
        return $this->model
            ->where('id_type_transaction', TypeTransactionModel::DEPOT_ID)
            ->findAll();
    }

}