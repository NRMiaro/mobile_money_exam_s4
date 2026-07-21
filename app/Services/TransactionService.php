<?php namespace App\Services;

use App\Models\TransactionModel;
use App\Models\TypeTransactionModel;
use App\Models\UtilisateurModel;
use App\Models\CommissionModel;
use App\Models\PromotionModel;
use Config\Database;
use Exception;

class TransactionService
{
    protected TransactionModel $transactionModel;
    protected UtilisateurModel $utilisateurModel;
    protected BaremeService $baremeService;
    protected PrefixeService $prefixeService;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->utilisateurModel = new UtilisateurModel();
        $this->baremeService = new BaremeService();
        $this->prefixeService = new PrefixeService();
    }

    public function getTotalGains(): float
    {
        $result = $this->transactionModel->selectSum('frais', 'total_gains')->first();
        return (float) ($result['total_gains'] ?? 0);
    }

    public function getHistoriqueClient(int $clientId): array
    {
        $rows = $this->transactionModel->findHistoriqueByClient($clientId);
        return $this->formatHistorique($rows, $clientId, false);
    }

    public function getRecentHistoriqueClient(int $clientId): array
    {
        $rows = $this->transactionModel->findRecentHistoriqueByClient($clientId);
        return $this->formatHistorique($rows, $clientId, true);
    }

    private function formatHistorique(array $rows, int $clientId, bool $includeCommission): array
    {
        $historique = [];
        foreach ($rows as $row) {
            $type = strtolower($row['type_libelle']);
            $sens = $this->determinerSens($type, $row, $clientId);
            $montant = $row['montant'] + ($includeCommission ? ($row['montant_commission'] ?? 0) : 0);

            $historique[] = [
                'date'    => date('d/m/Y H:i', strtotime($row['date_transaction'])),
                'type'    => $type,
                'montant' => $montant,
                'frais'   => $row['frais'],
                'sens'    => $sens,
            ];
        }
        return $historique;
    }

    protected function determinerSens(string $type, array $row, int $clientId): string
    {
        if ($type === 'depot') return '+';
        if ($type === 'retrait') return '-';
        if ($type === 'transfert') {
            return $row['id_client_source'] == $clientId ? '-' : '+';
        }
        return '+';
    }

    /**
     * Effectuer un Dépôt
     */
    public function effectuerDepot(int $idUtilisateur, float $montant): void
    {
        $frais = $this->baremeService->calculerFrais($montant, TypeTransactionModel::DEPOT_ID);

        $db = Database::connect();
        $db->transStart();

        $utilisateur = $this->utilisateurModel->find($idUtilisateur);
        $this->utilisateurModel->update($idUtilisateur, [
            'solde' => $utilisateur['solde'] + $montant - $frais
        ]);

        $this->transactionModel->insert([
            'id_type_transaction'    => TypeTransactionModel::DEPOT_ID,
            'id_client_destinataire' => $idUtilisateur,
            'montant'                => $montant,
            'frais'                  => $frais,
            'date_transaction'       => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception("Erreur lors du traitement du dépôt.");
        }
    }

    /**
     * Effectuer un Retrait
     */
    public function effectuerRetrait(int $idUtilisateur, float $montant): void
    {
        $frais = $this->baremeService->calculerFrais($montant, TypeTransactionModel::RETRAIT_ID);

        $utilisateur = $this->utilisateurModel->find($idUtilisateur);
        $totalDebite = $montant + $frais;

        if ($utilisateur['solde'] < $totalDebite) {
            throw new Exception("Solde insuffisant pour effectuer ce retrait.");
        }

        $db = Database::connect();
        $db->transStart();

        $this->utilisateurModel->update($idUtilisateur, [
            'solde' => $utilisateur['solde'] - $totalDebite
        ]);

        $this->transactionModel->insert([
            'id_type_transaction' => TypeTransactionModel::RETRAIT_ID,
            'id_client_source'    => $idUtilisateur,
            'montant'             => $montant,
            'frais'               => $frais,
            'date_transaction'    => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception("Erreur lors du traitement du retrait.");
        }
    }

    /**
     * Effectuer un Transfert simple
     */
    public function effectuerTransfert(int $idUtilisateur, float $montant, string $numeroDestinataire, bool $payerRetrait): bool
    {
        $expediteur = $this->utilisateurModel->find($idUtilisateur);
        $destinataire = $this->utilisateurModel->where('numero', $numeroDestinataire)->first();

        if (!$destinataire) {
            throw new Exception('Numéro destinataire introuvable.');
        }

        if ($destinataire['id'] == $idUtilisateur) {
            throw new Exception("Vous ne pouvez pas vous transférer de l'argent.");
        }

        $operateurSource = $this->prefixeService->getOperateurByNumero($expediteur['numero']);
        $operateurDestinataire = $this->prefixeService->getOperateurByNumero($numeroDestinataire);

        if (!$operateurDestinataire) {
            throw new Exception('Opérateur du destinataire inconnu.');
        }

        $fraisTransfert = $this->baremeService->calculerFrais($montant, TypeTransactionModel::TRANSFERT_ID);

        if ($operateurSource == $operateurDestinataire) {
            $promotionModel = new PromotionModel();
            $promoActive = $promotionModel->getPromoActive();
            
                if ($promoActive) {
                    $reduction = $fraisTransfert * ($promoActive['pourcentage']/100);
                    $fraisTransfert = $fraisTransfert - $reduction;
                }
        }

        $fraisRetrait = 0;
        if ($payerRetrait && $operateurSource == $operateurDestinataire) {
            $fraisRetrait = $this->baremeService->calculerFrais($montant, TypeTransactionModel::RETRAIT_ID);
        }

        $commission = 0;
        if ($operateurSource != $operateurDestinataire) {
            $commissionModel = new CommissionModel();
            $commissionOp = $commissionModel->where('id_operateur', $operateurDestinataire)->first();
            if ($commissionOp) {
                $commission = ($montant * $commissionOp['pct_commission']) / 100;
            }
        }

        $totalDebite = $montant + $fraisTransfert + $fraisRetrait + $commission;

        if ($expediteur['solde'] < $totalDebite) {
            throw new Exception('Solde insuffisant.');
        }

        $db = Database::connect();
        $db->transStart();

        $this->utilisateurModel->update($idUtilisateur, [
            'solde' => $expediteur['solde'] - $totalDebite
        ]);

        $this->utilisateurModel->update($destinataire['id'], [
            'solde' => $destinataire['solde'] + $montant + $fraisRetrait
        ]);

        $this->transactionModel->insert([
            'id_type_transaction'       => TypeTransactionModel::TRANSFERT_ID,
            'id_client_source'          => $idUtilisateur,
            'id_client_destinataire'    => $destinataire['id'],
            'id_operateur_destinataire' => $operateurDestinataire,
            'montant'                   => $montant,
            'frais'                     => $fraisTransfert + $fraisRetrait,
            'montant_commission'        => $commission,
            'date_transaction'          => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception("Erreur lors de la réalisation du transfert.");
        }

        return $fraisRetrait > 0;
    }

    /**
     * Effectuer un Transfert Multiple
     */
    public function effectuerTransfertMultiple(int $idUtilisateur, float $montantTotal, array $numeros): void
    {
        if (empty($numeros)) {
            throw new Exception('Aucun destinataire.');
        }

        $expediteur = $this->utilisateurModel->find($idUtilisateur);
        $destinataires = [];

        foreach ($numeros as $numero) {
            $numeroClean = str_replace(' ', '', $numero);
            $client = $this->utilisateurModel->where('numero', $numeroClean)->first();

            if (!$client) {
                throw new Exception("Le numéro {$numeroClean} n'existe pas.");
            }
            if ($client['id'] == $idUtilisateur) {
                throw new Exception('Impossible de vous transférer à vous-même.');
            }
            $destinataires[] = $client;
        }

        $operateurExp = $this->prefixeService->getOperateurByNumero($expediteur['numero']);

        foreach ($destinataires as $dest) {
            $opDest = $this->prefixeService->getOperateurByNumero($dest['numero']);
            if ($opDest != $operateurExp) {
                throw new Exception('Tous les numéros doivent appartenir au même opérateur que vous.');
            }
        }

        $fraisTotal = $this->baremeService->calculerFrais($montantTotal, TypeTransactionModel::TRANSFERT_ID);

        $nb = count($destinataires);
        $montantPart = floor($montantTotal / $nb);
        $fraisPart = $fraisTotal / $nb;
        $totalDebite = $montantTotal + $fraisTotal;

        if ($expediteur['solde'] < $totalDebite) {
            throw new Exception('Solde insuffisant.');
        }

        $db = Database::connect();
        $db->transStart();

        $this->utilisateurModel->update($idUtilisateur, [
            'solde' => $expediteur['solde'] - $totalDebite
        ]);

        foreach ($destinataires as $dest) {
            $this->utilisateurModel->update($dest['id'], [
                'solde' => $dest['solde'] + $montantPart
            ]);

            $this->transactionModel->insert([
                'id_type_transaction'    => TypeTransactionModel::TRANSFERT_ID,
                'id_client_source'       => $idUtilisateur,
                'id_client_destinataire' => $dest['id'],
                'montant'                => $montantPart,
                'frais'                  => $fraisPart,
                'date_transaction'       => date('Y-m-d H:i:s')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception("Erreur lors du transfert multiple.");
        }
    }

    public function getSituationGain()
    {
        $gainOperateur = $this->transactionModel->selectSum('frais')
            ->groupStart()
            ->whereIn('id_type_transaction', [TypeTransactionModel::DEPOT_ID, TypeTransactionModel::RETRAIT_ID])
            ->orGroupStart()
            ->where('id_type_transaction', TypeTransactionModel::TRANSFERT_ID)
            ->groupStart()
            ->where('id_operateur_destinataire', TransactionModel::OPERATEUR_ID)
            ->orWhere('id_operateur_destinataire', null)
            ->groupEnd()
            ->groupEnd()
            ->groupEnd()
            ->get()
            ->getRow()
            ->frais ?? 0;

        $gainAutres = $this->transactionModel->selectSum('frais')
            ->where('id_type_transaction', TypeTransactionModel::TRANSFERT_ID)
            ->where('id_operateur_destinataire !=', TransactionModel::OPERATEUR_ID)
            ->where('id_operateur_destinataire IS NOT NULL')
            ->get()
            ->getRow()
            ->frais ?? 0;

        return [
            'gain_operateur'         => (float) $gainOperateur,
            'gain_autres_operateurs' => (float) $gainAutres,
        ];
    }

    public function getSituationCommissionParOperateur(): array
    {
        return $this->transactionModel->select('operateur.libelle as operateur, SUM(transactions.montant_commission) as total_commission')
            ->join('operateur', 'operateur.id = transactions.id_operateur_destinataire')
            ->where('transactions.id_type_transaction', TypeTransactionModel::TRANSFERT_ID)
            ->where('transactions.id_operateur_destinataire !=', TransactionModel::OPERATEUR_ID) // exclut Yas
            ->groupBy('operateur.libelle')
            ->orderBy('total_commission', 'DESC')
            ->findAll();
    }

}