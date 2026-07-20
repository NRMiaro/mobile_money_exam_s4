<?php

namespace App\Controllers;

use App\Models\TypeTransactionModel;
use App\Models\UtilisateurModel;
use App\Services\TransactionService;

class ClientController extends BaseController
{
    
    public function dashboard(){
        $id = session()->get('idUtilisateur');
        $utilisateurModel = new UtilisateurModel();
        $transactionService = new TransactionService();
        $utilisateur = $utilisateurModel->find($id);
        $data=[
            'utilisateur' => $utilisateur,
            'transactions' => $transactionService->getRecentHistoriqueClient($id)
        ];
        return view('client/dashboard',$data);
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
            'solde' => $utilisateur['solde'] + $montant - $frais
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
    public function retrait()
    {
        return view('client/retrait');
    }

    public function effectuerRetrait()
    {
        $idUtilisateur = session()->get('idUtilisateur');
        $montant = (float) $this->request->getPost('montant');

        // Recherche du barème
        $frais = 0;
        $baremesRetrait = (new \App\Services\BaremeService())->getBaremesRetrait();

        foreach ($baremesRetrait as $bareme) {
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
            'solde' => $utilisateur['solde'] - $montant - $frais
        ]);

        // Enregistrement de la transaction
        $transactionModel = new \App\Models\TransactionModel();

        $transactionModel->insert([
            'id_type_transaction'   => TypeTransactionModel::RETRAIT_ID, // DEPOT
            'id_client_source'      => $idUtilisateur,
            // 'id_client_destinataire' => ,
            'montant'               => $montant,
            'frais'                 => $frais,
            'date_transaction'      => date('Y-m-d H:i:s')
        ]);

        return redirect()
            ->to('/client/solde')
            ->with('success', 'Dépôt effectué avec succès.');
    }

    public function transfert()
    {
        return view('client/transfert');
    }

    public function effectuerTransfert()
    {
        $idUtilisateur = session()->get('idUtilisateur');
        $montant = (float) $this->request->getPost('montant');
        $numeroDestinataire = str_replace(
            ' ',
            '',
            $this->request->getPost('numero_destinataire')
        );

        // Recherche du barème
        $frais = 0;
        $baremesTransfert = (new \App\Services\BaremeService())->getBaremesTransfert();

        foreach ($baremesTransfert as $bareme) {
            if (
                $bareme['montant_min'] <= $montant &&
                $bareme['montant_max'] >= $montant
            ) {
                $frais = $bareme['frais'];
                break;
            }
        }

        $utilisateurModel = new UtilisateurModel();

        $utilisateur = $utilisateurModel->find($idUtilisateur);

        $destinataire = $utilisateurModel
            ->where('numero', $numeroDestinataire)
            ->first();

        if (!$destinataire) {
            return redirect()
                ->to(base_url('/client/transfert'))
                ->with('error', 'Numéro destinataire introuvable.');
        }

        if ($destinataire['id'] == $idUtilisateur) {
            return redirect()
                ->to(base_url('/client/transfert'))
                ->with('error', 'Vous ne pouvez pas vous transférer de l\'argent.');
        }

        if ($utilisateur['solde'] < $montant + $frais) {
            return redirect()
                ->to(base_url('/client/transfert'))
                ->with('error', 'Solde insuffisant.');
        }

        // Débiter l'expéditeur
        $utilisateurModel->update($idUtilisateur, [
            'solde' => $utilisateur['solde'] - $montant - $frais
        ]);

        // Créditer le destinataire
        $utilisateurModel->update($destinataire['id'], [
            'solde' => $destinataire['solde'] + $montant
        ]);

        // Enregistrement de la transaction
        $transactionModel = new \App\Models\TransactionModel();

        $transactionModel->insert([
            'id_type_transaction'    => TypeTransactionModel::TRANSFERT_ID,
            'id_client_source'       => $idUtilisateur,
            'id_client_destinataire' => $destinataire['id'],
            'montant'                => $montant,
            'frais'                  => $frais,
            'date_transaction'       => date('Y-m-d H:i:s')
        ]);

        return redirect()
            ->to('/client/solde')
            ->with('success', 'Transfert effectué avec succès.');
    }


}
