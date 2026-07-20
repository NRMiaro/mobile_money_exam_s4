<?php

namespace App\Controllers;

use App\Models\TypeTransactionModel;
use App\Models\UtilisateurModel;
use App\Models\CommissionModel;
use App\Models\PrefixeModel;
use App\Services\TransactionService;
use App\Services\BaremeService;

class ClientController extends BaseController
{

    public function dashboard()
    {
        $id = session()->get('idUtilisateur');
        $utilisateurModel = new UtilisateurModel();
        $transactionService = new TransactionService();
        $utilisateur = $utilisateurModel->find($id);
        $data = [
            'utilisateur' => $utilisateur,
            'transactions' => $transactionService->getRecentHistoriqueClient($id)
        ];
        return view('client/dashboard', $data);
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
        return view('client/depot', [
            'baremes' => (new BaremeService())->getBaremesDepot()
        ]);
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
        return view('client/retrait', [
            'baremes' => (new BaremeService())->getBaremesRetrait()
        ]);
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
        $modelPrefixe = (new PrefixeModel())->select();

        $prefixes = $modelPrefixe
            ->select('prefixe, id_operateur')
            ->get()
            ->getResultArray();


        return view('client/transfert', [
            'baremes' => (new BaremeService())->getBaremesTransfert(),
            'baremesRetrait' => (new BaremeService())->getBaremesRetrait(),
            'prefixes' => $prefixes,
            'commissions' => (new CommissionModel())->findAll(),

            // opérateur connecté
            'idOperateur' => 1
        ]);
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

        $payerRetrait = $this->request->getPost('payer_retrait') !== null;


        $baremeService = new BaremeService();


        /*
    |--------------------------------------------------------------------------
    | Recherche des utilisateurs
    |--------------------------------------------------------------------------
    */

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



        /*
    |--------------------------------------------------------------------------
    | Détermination des opérateurs
    |--------------------------------------------------------------------------
    */

        $prefixeService = new \App\Services\PrefixeService();


        // opérateur du client connecté
        $operateurSource =
            $prefixeService->getOperateurByNumero(
                $utilisateur['numero']
            );


        // opérateur du destinataire
        $operateurDestinataire =
            $prefixeService->getOperateurByNumero(
                $numeroDestinataire
            );


        if (!$operateurDestinataire) {

            return redirect()
                ->to(base_url('/client/transfert'))
                ->with('error', 'Opérateur du destinataire inconnu.');
        }



        /*
    |--------------------------------------------------------------------------
    | Frais de transfert
    |--------------------------------------------------------------------------
    */

        $frais = 0;

        foreach ($baremeService->getBaremesTransfert() as $bareme) {

            if (
                $montant >= $bareme['montant_min']
                &&
                $montant <= $bareme['montant_max']
            ) {

                $frais = $bareme['frais'];
                break;
            }
        }



        /*
    |--------------------------------------------------------------------------
    | Frais de retrait du destinataire
    | Possible uniquement même opérateur
    |--------------------------------------------------------------------------
    */

        $fraisRetrait = 0;


        if (
            $payerRetrait
            &&
            $operateurSource == $operateurDestinataire
        ) {

            foreach ($baremeService->getBaremesRetrait() as $bareme) {

                if (
                    $montant >= $bareme['montant_min']
                    &&
                    $montant <= $bareme['montant_max']
                ) {

                    $fraisRetrait = $bareme['frais'];
                    break;
                }
            }
        }



        /*
    |--------------------------------------------------------------------------
    | Commission inter-opérateur
    |--------------------------------------------------------------------------
    */

        $commission = 0;


        if ($operateurSource != $operateurDestinataire) {

            $commissionModel = new \App\Models\CommissionModel();


            $commissionOperateur = $commissionModel
                ->where(
                    'id_operateur',
                    $operateurDestinataire
                )
                ->first();


            if ($commissionOperateur) {

                $commission =
                    $montant
                    *
                    $commissionOperateur['pct_commission']
                    /
                    100;
            }
        }



        /*
    |--------------------------------------------------------------------------
    | Vérification du solde
    |--------------------------------------------------------------------------
    */

        $totalDebite =
            $montant
            +
            $frais
            +
            $fraisRetrait
            +
            $commission;


        if ($utilisateur['solde'] < $totalDebite) {

            return redirect()
                ->to(base_url('/client/transfert'))
                ->with('error', 'Solde insuffisant.');
        }



        /*
    |--------------------------------------------------------------------------
    | Mise à jour des soldes
    |--------------------------------------------------------------------------
    */

        // Débit expéditeur

        $utilisateurModel->update($idUtilisateur, [

            'solde' =>
            $utilisateur['solde']
                -
                $totalDebite
        ]);



        // Crédit destinataire

        $utilisateurModel->update($destinataire['id'], [

            'solde' =>
            $destinataire['solde']
                +
                $montant
                +
                $fraisRetrait
        ]);



        /*
    |--------------------------------------------------------------------------
    | Historique transaction
    |--------------------------------------------------------------------------
    */

        $transactionModel = new \App\Models\TransactionModel();


        $transactionModel->insert([

            'id_type_transaction'
            => TypeTransactionModel::TRANSFERT_ID,

            'id_client_source'
            => $idUtilisateur,

            'id_client_destinataire'
            => $destinataire['id'],

            'id_operateur_destinataire'
            => $operateurDestinataire,

            'montant'
            => $montant,

            'frais'
            =>
            $frais
                +
                $fraisRetrait,

            'montant_commission'
            => $commission,

            'date_transaction'
            => date('Y-m-d H:i:s')
        ]);



        return redirect()
            ->to('/client/solde')
            ->with(
                'success',
                $fraisRetrait > 0
                    ? 'Transfert effectué avec prise en charge des frais de retrait.'
                    : 'Transfert effectué avec succès.'
            );
    }

    public function transfertMultiple()
    {
        return view('client/transfert_multiple', [
            'baremes' => (new BaremeService())
                ->getBaremesTransfert(),

            'commissions' => (new \App\Models\CommissionModel())
                ->findAll()
        ]);
    }

    public function effectuerTransfertMultiple()
    {
        $idUtilisateur = session()->get('idUtilisateur');
        $montantTotal = (float) $this->request->getPost('montant');
        $numeros = $this->request->getPost('numeros');
        if (!$numeros || count($numeros) == 0) {
            return redirect()
                ->back()
                ->with('error', 'Aucun destinataire.');
        }

        $utilisateurModel = new UtilisateurModel();
        $expediteur = $utilisateurModel->find($idUtilisateur);

        /*
    |--------------------------------------------------------------------------
    | Recherche destinataires
    |--------------------------------------------------------------------------
    */

        $destinataires = [];
        foreach ($numeros as $numero) {
            $numero = str_replace(' ', '', $numero);
            $client = $utilisateurModel
                ->where('numero', $numero)
                ->first();
            if (!$client) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        "Le numéro $numero n'existe pas."
                    );
            }
            if ($client['id'] == $idUtilisateur) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Impossible de vous transférer à vous-même.'
                    );
            }
            $destinataires[] = $client;
        }
        /*
    |--------------------------------------------------------------------------
    | Vérification même opérateur
    |--------------------------------------------------------------------------
    */
        $prefixeService = new \App\Services\PrefixeService();
        $prefixExp = substr($expediteur['numero'], 0, 3);
        $prefixDest = substr(
            $destinataires[0]['numero'],
            0,
            3
        );
        $operateurExp = null;
        $operateurDest = null;
        foreach ($prefixeService->getAll() as $prefix) {
            if ($prefix['prefixe'] == $prefixExp) {
                $operateurExp = $prefix['id_operateur'];
            }
            if ($prefix['prefixe'] == $prefixDest) {
                $operateurDest = $prefix['id_operateur'];
            }
        }

        if ($operateurExp != $operateurDest) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Les transferts multiples doivent être vers le même opérateur.'
                );
        }

        foreach ($destinataires as $dest) {
            $op = substr($dest['numero'], 0, 3);
            foreach ($prefixeService->getAll() as $prefix) {
                if (
                    $prefix['prefixe'] == $op
                    &&
                    $prefix['id_operateur'] != $operateurExp
                ) {
                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'Tous les numéros doivent appartenir au même opérateur.'
                        );
                }
            }
        }





        /*
    |--------------------------------------------------------------------------
    | Calcul frais
    |--------------------------------------------------------------------------
    */


        $baremeService = new BaremeService();
        $fraisTotal = 0;
        foreach (
            $baremeService->getBaremesTransfert()
            as $bareme
        ) {
            if (
                $montantTotal >= $bareme['montant_min']
                &&
                $montantTotal <= $bareme['montant_max']
            ) {

                $fraisTotal = $bareme['frais'];
                break;
            }
        }
        $nb = count($destinataires);
        $montantPart = floor($montantTotal / $nb);
        $fraisPart =
            $fraisTotal / $nb;
        $totalDebite =
            $montantTotal + $fraisTotal;
        if ($expediteur['solde'] < $totalDebite) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Solde insuffisant.'
                );
        }




        /*
    |--------------------------------------------------------------------------
    | Mise à jour comptes
    |--------------------------------------------------------------------------
    */


        $utilisateurModel->update(
            $idUtilisateur,
            [
                'solde' =>
                $expediteur['solde'] - $totalDebite
            ]
        );



        $transactionModel =
            new \App\Models\TransactionModel();




        foreach ($destinataires as $dest) {


            $utilisateurModel->update(
                $dest['id'],
                [
                    'solde' =>
                    $dest['solde'] + $montantPart
                ]
            );



            $transactionModel->insert([

                'id_type_transaction'
                => TypeTransactionModel::TRANSFERT_ID,

                'id_client_source'
                => $idUtilisateur,

                'id_client_destinataire'
                => $dest['id'],

                'montant'
                => $montantPart,

                'frais'
                => $fraisPart,

                'date_transaction'
                => date('Y-m-d H:i:s')

            ]);
        }



        return redirect()
            ->to('/client/solde')
            ->with(
                'success',
                'Transfert multiple effectué avec succès.'
            );
    }
}
