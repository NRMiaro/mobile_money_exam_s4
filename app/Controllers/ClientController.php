<?php

namespace App\Controllers;

use App\Models\TypeTransactionModel;
use App\Models\UtilisateurModel;

class ClientController extends BaseController
{
    public function index()
    {
        return view('client/index');
    }

    public function solde()
    {
        $utilisateurModel = new UtilisateurModel();

        $id = session()->get('idUtilisateur');

        $utilisateur = $utilisateurModel->find($id);

        return view('client/solde', [
            'utilisateur' => $utilisateur
        ]);
    }

    public function depot()
    {
        return view('client/depot');
    }

    public function effectuerDepot()
    {
        $idUtilisateur = session()->get('idUtilisateur');
        $montant = (float) $this->request->getPost('montant');

        // Recherche du barème
        $frais = 0;
        $baremesDepot = (new \App\Services\BaremeService())->getBaremesDepot();

        foreach ($baremesDepot as $bareme) {
            if (
                $bareme['montant_min'] <= $montant &&
                $bareme['montant_max'] >= $montant
            ) {
                $frais = $bareme['frais'];
                break;
            }
        }

        // Mise à jour du solde
        $utilisateurModel = new UtilisateurModel();
        $utilisateur = $utilisateurModel->find($idUtilisateur);

        $utilisateurModel->update($idUtilisateur, [
            'solde' => $utilisateur['solde'] + $montant
        ]);

        // Enregistrement de la transaction
        $transactionModel = new \App\Models\TransactionModel();

        $transactionModel->insert([
            'id_type_transaction'   => TypeTransactionModel::DEPOT_ID, // DEPOT
            // 'id_client_source'      => null,
            'id_client_destinataire' => $idUtilisateur,
            'montant'               => $montant,
            'frais'                 => $frais,
            'date_transaction'      => date('Y-m-d H:i:s')
        ]);

        return redirect()
            ->to('/client/solde')
            ->with('success', 'Dépôt effectué avec succès.');
    }

    public function historique(): string
{
    $clientId = session()->get('idUtilisateur');

    $transactionService = new \App\Services\TransactionService();

    return view('client/historique', [
        'transactions' => $transactionService->getHistoriqueClient($clientId),
    ]);
}
}
