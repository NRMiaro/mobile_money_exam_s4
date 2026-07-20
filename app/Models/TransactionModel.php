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

    protected array $casts = [
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

    public function findByClient(int $clientId): array
    {
        return $this->select('transactions.*, type_transaction.libelle as type_libelle')
            ->join('type_transaction', 'type_transaction.id = transactions.id_type_transaction')
            ->groupStart()
                ->where('transactions.id_client_source', $clientId)
                ->orWhere('transactions.id_client_destinataire', $clientId)
            ->groupEnd()
            ->orderBy('transactions.date_transaction', 'DESC')
            ->findAll();
    }
}