<?php 
namespace App\Services;
use App\Models\TransactionModel;

class TransactionService {

    public function getTotalGains(): float
    {
        $modelTransaction = new TransactionModel();
        $result = $modelTransaction->selectSum('frais', 'total_gains')->first();
        return (float) ($result['total_gains'] ?? 0);
    }

}