<?php

namespace App\Models;

use CodeIgniter\Model;

class ChoixEpargneModel extends Model
{
    protected $table = 'choix_epargne';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_client',
        'pourcentage'
    ];

    protected array $casts = [
        'id' => 'int',
        'id_client' => 'int',
        'pourcentage' => 'float'
    ];

    protected $validationRules = [
        'id_client' => 'required|integer',
        'pourcentage' => 'required|numeric'
    ];

    public function getByIdClient($idClient){
        return $this->where('id_client', $idClient)
            ->first();
    }
}