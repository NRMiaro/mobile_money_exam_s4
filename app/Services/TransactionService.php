<?php

namespace App\Services;

use App\Models\TransactionModel;
use App\Models\TypeTransactionModel;
use App\Models\UtilisateurModel;

class TransactionService
{

    protected TransactionModel $model;
    public function __construct()
    {
        $this->model = new TransactionModel();
    }


    public function getTotalGains(): float
    {
        $modelTransaction = new TransactionModel();
        $result = $modelTransaction->selectSum('frais', 'total_gains')->first();
        return (float) ($result['total_gains'] ?? 0);
    }

    public function getHistoriqueClient(int $clientId): array
    {
        $rows = $this->model->findHistoriqueByClient($clientId);

        $historique = [];

        foreach ($rows as $row) {
            $type = strtolower($row['type_libelle']); // DEPOT -> depot, etc.
            $sens = $this->determinerSens($type, $row, $clientId);

            $historique[] = [
                'date'    => date('d/m/Y H:i', strtotime($row['date_transaction'])),
                'type'    => $type,
                'montant' => $row['montant'],
                'frais'   => $row['frais'],
                'sens'    => $sens,
            ];
        }

        return $historique;
    }

    public function getRecentHistoriqueClient(int $clientId): array
    {
        $rows = $this->model->findRecentHistoriqueByClient($clientId);

        $historique = [];

        foreach ($rows as $row) {
            $type = strtolower($row['type_libelle']); // DEPOT -> depot, etc.
            $sens = $this->determinerSens($type, $row, $clientId);

            $historique[] = [
                'date'    => date('d/m/Y H:i', strtotime($row['date_transaction'])),
                'type'    => $type,
                'montant' => $row['montant'],
                'frais'   => $row['frais'],
                'sens'    => $sens,
            ];
        }

        return $historique;
    }

    protected function determinerSens(string $type, array $row, int $clientId): string
    {
        if ($type === 'depot') {
            return '+';
        }

        if ($type === 'retrait') {
            return '-';
        }

        if ($type === 'transfert') {
            return $row['id_client_source'] == $clientId ? '-' : '+';
        }

        return '+';
    }
}
