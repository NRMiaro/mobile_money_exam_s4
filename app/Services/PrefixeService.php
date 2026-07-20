<?php 
namespace App\Services;
use App\Models\PrefixeModel;
class PrefixeService {

    public function getTotalPrefixes(){
        $prefixeModel = new PrefixeModel();

        return $prefixeModel->countAllResults();
    }


}