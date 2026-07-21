<?php

namespace App\Controllers;

use App\Models\ChoixEpargneModel;
use App\Models\CommissionModel;
use App\Models\PrefixeModel;
use App\Models\PromotionModel;
use App\Services\BaremeService;
use App\Services\TransactionService;
use App\Services\UtilisateurService;
use Exception;

class ClientController extends BaseController
{
    protected TransactionService $transactionService;
    protected BaremeService $baremeService;
    protected UtilisateurService $utilisateurService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->baremeService = new BaremeService();
        $this->utilisateurService = new UtilisateurService();
    }

    public function dashboard()
    {
        $id = session()->get('idUtilisateur');

        return view('client/dashboard', [
            'utilisateur'  => $this->utilisateurService->getById($id),
            'transactions' => $this->transactionService->getRecentHistoriqueClient($id)
        ]);
    }

    public function solde()
    {
        $id = session()->get('idUtilisateur');

        return view('client/solde', [
            'utilisateur' => $this->utilisateurService->getById($id)
        ]);
    }

    public function depot()
    {
        return view('client/depot', [
            'baremes' => $this->baremeService->getBaremesDepot()
        ]);
    }

    public function effectuerDepot()
    {
        $idUtilisateur = session()->get('idUtilisateur');
        $montant = (float) $this->request->getPost('montant');

        try {
            $this->transactionService->effectuerDepot($idUtilisateur, $montant);
            return redirect()->to('/client/solde')->with('success', 'Dépôt effectué avec succès.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function historique(): string
    {
        $clientId = session()->get('idUtilisateur');

        return view('client/historique', [
            'transactions' => $this->transactionService->getHistoriqueClient($clientId),
        ]);
    }

    public function retrait()
    {
        return view('client/retrait', [
            'baremes' => $this->baremeService->getBaremesRetrait()
        ]);
    }

    public function effectuerRetrait()
    {
        $idUtilisateur = session()->get('idUtilisateur');
        $montant = (float) $this->request->getPost('montant');

        try {
            $this->transactionService->effectuerRetrait($idUtilisateur, $montant);
            // return redirect()->to('/client/solde')->with('success', 'Retrait effectué avec succès.');
        } catch (Exception $e) {
            // return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function transfert()
    {
        $modelPrefixe = new PrefixeModel();
        $prefixes = $modelPrefixe->select('prefixe, id_operateur')->get()->getResultArray();
        $promotionModel = new PromotionModel();
        $promoActive = $promotionModel->getPromoActive();
        

        return view('client/transfert', [
            'baremes'        => $this->baremeService->getBaremesTransfert(),
            'baremesRetrait' => $this->baremeService->getBaremesRetrait(),
            'prefixes'       => $prefixes,
            'commissions'    => (new CommissionModel())->findAll(),
            'idOperateur'    => 1,
            'promoActive'    => $promoActive,
        ]);
    }

    public function effectuerTransfert()
    {
        $idUtilisateur = session()->get('idUtilisateur');
        $montant = (float) $this->request->getPost('montant');
        $numeroDestinataire = str_replace(' ', '', $this->request->getPost('numero_destinataire') ?? '');
        $payerRetrait = $this->request->getPost('payer_retrait') !== null;

        try {
            $fraisRetraitInclus = $this->transactionService->effectuerTransfert(
                $idUtilisateur,
                $montant,
                $numeroDestinataire,
                $payerRetrait
            );

            $msg = $fraisRetraitInclus
                ? 'Transfert effectué avec prise en charge des frais de retrait.'
                : 'Transfert effectué avec succès.';

            return redirect()->to('/client/solde')->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->to(base_url('/client/transfert'))->with('error', $e->getMessage());
        }
    }

    public function transfertMultiple()
    {
        $data = [
            'baremes'     => $this->baremeService->getBaremesTransfert(),
            'commissions' => (new CommissionModel())->findAll()
        ];
        return view('client/transfert_multiple', $data);
    }

    public function effectuerTransfertMultiple()
    {
        $idUtilisateur = session()->get('idUtilisateur');
        $montantTotal = (float) $this->request->getPost('montant');
        $numeros = $this->request->getPost('numeros') ?? [];

        try {
            $this->transactionService->effectuerTransfertMultiple($idUtilisateur, $montantTotal, $numeros);
            return redirect()->to('/client/solde')->with('success', 'Transfert multiple effectué avec succès.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function choixEpargne(){
        $data = [
            'pourcentageEpargne' => (new ChoixEpargneModel())->getByIdClient(session()->get('idUtilisateur'))['pourcentage']
        ];
        return view('client/choix-epargne', $data);
    }

    public function effectuerChoixEpargne(){
        $pourcentage = $this->request->getPost('pourcentage');
        $idUtilisateur = session()->get('idUtilisateur');
        $model = new ChoixEpargneModel();
        $choixActuel = $model->getByIdClient($idUtilisateur);
        (new ChoixEpargneModel())->update(
            $choixActuel['id'],
            [
                'pourcentage' => $pourcentage
            ]
        );

        return redirect()->to('client')->with('success', "Pourcentage change avec succes");
    }
}