<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table = 'commission';
    protected $primaryKey = 'id';

    protected $useTimestamps = false;

    protected $allowedFields = [
        'id_operateur',
        'pct_commission'
    ];

    protected array $casts = [
        'id' => 'int',
        'id_operateur' => 'int',
        'pct_commission' => 'float',
    ];

    protected $validationRules = [
        'id_operateur' => 'required|integer',
        'pct_commission' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];

    public function getCommissionByOperateur(int $idOperateur): ?array
    {
        return $this->where('id_operateur', $idOperateur)->first();
    }
}