<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';

    protected $useTimestamps = false;

    protected $allowedFields = [
        'id_type_transaction',
        'id_client_source',
        'id_client_destinataire',
        'montant',
        'frais'
    ];

    protected $casts = [
        'id' => 'int',
        'id_type_transaction' => 'int',
        'id_client_source' => 'int',
        'id_client_destinataire' => 'int',
        'montant' => 'float',
        'frais' => 'float',
    ];

    protected $validationRules = [
        'id_type_transaction' => 'required|integer',
        'montant' => 'required|numeric',
        'frais' => 'required|numeric'
    ];
}