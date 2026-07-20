<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeTransactionModel extends Model
{

    public const DEPOT_ID = 1;
    public const RETRAIT_ID = 2;
    public const TRANSFERT_ID = 3;

    protected $table = 'type_transaction';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'libelle'
    ];

    protected array $casts = [
        'id' => 'int'
    ];
}