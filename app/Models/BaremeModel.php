<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeModel extends Model
{
    protected $table = 'bareme';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_type_transaction',
        'montant_min',
        'montant_max',
        'frais'
    ];

    protected $casts = [
        'id' => 'int',
        'id_type_transaction' => 'int',
        'montant_min' => 'float',
        'montant_max' => 'float',
        'frais' => 'float'
    ];

    protected $validationRules = [
        'id_type_transaction' => 'required|integer',
        'montant_min' => 'required|numeric',
        'montant_max' => 'required|numeric',
        'frais' => 'required|numeric'
    ];
}