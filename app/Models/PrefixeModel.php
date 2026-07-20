<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table = 'prefixe';
    protected $primaryKey = 'id';

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    protected $allowedFields = [
        'prefixe',
        'id_operateur'
    ];

    protected  array $casts = [
        'id' => 'int',
        'id_operateur' => 'int',
    ];

    protected $validationRules = [
        'prefixe' => 'required|exact_length[3]|numeric|is_unique[prefixe.prefixe,id,{id}]',
        'id_operateur' => 'required',
    ];

    protected $validationMessages = [
        'prefixe' => [
            'required'     => 'Le préfixe est obligatoire.',
            'exact_length' => 'Le préfixe doit contenir exactement 3 chiffres.',
            'numeric'      => 'Le préfixe ne doit contenir que des chiffres.',
            'is_unique'    => 'Ce préfixe existe déjà.',
        ],
    ];
}