<?php

namespace App\Controllers;

use App\Models\TypeTransactionModel;
use App\Models\UtilisateurModel;
use App\Services\BaremeService;
use App\Services\PrefixeService;
use App\Services\TransactionService;
use App\Services\UtilisateurService;
use Exception;

class OperateurController extends BaseController
{

    private BaremeService $baremeService;
    private TypeTransactionModel $modelTypeTransaction;

    public function __construct()
    {
        $this->baremeService = new BaremeService();
        $this->modelTypeTransaction = new TypeTransactionModel();
    }

    public function dashboard()
    {
        $transactionService = new TransactionService();
        $prefixeService = new PrefixeService();
        $utilisateurService = new UtilisateurService();
        $situationGain = $transactionService->getSituationGain();
        $data = [
            'totalGains' => $transactionService->getTotalGains(),
            'totalPrefixes' => $prefixeService->getTotalPrefixes(),
            'nombreClientsActifs' => $utilisateurService->countClientsActifs(),
            'gainOperateur'=>$situationGain['gain_operateur'],
            'gainAutresOperateurs' => $situationGain['gain_autres_operateurs']
        ];

        return view("operateur/dashboard", $data);
    }



    // ---------- OPERATEUR ----------
    public function operateurDashboard(): string
    {
        return view('operateur/dashboard');
    }

    public function operateurPrefixesIndex(): string
    {
        return view('operateur/prefixe/index');
    }

    public function operateurPrefixesCreate(): string
    {
        return view('operateur/prefixe/create');
    }

    public function operateurBaremesIndex(): string
    {
        return view('operateur/bareme/index', [
            'baremesDepot' => $this->baremeService->getBaremesDepot(),
            'baremesRetrait' => $this->baremeService->getBaremesRetrait(),
            'baremesTransfert' => $this->baremeService->getBaremesTransfert(),
        ]);
    }

    public function operateurBaremesCreate(): string
    {
        $data = [
            'typesTransaction' => $this->modelTypeTransaction
                ->orderBy('id', 'asc')
                ->findAll(),
            'baremes' => $this->baremeService->findAllIndexedByTypeTransaction()
        ];
        return view('operateur/bareme/create', $data);
    }

    public function operateurBaremesEdit($id)
    {
        return view('operateur/bareme/edit', [
            'contexte' => $this->baremeService
                ->getContexteModification($id)
        ]);
    }

    public function store()
    {
        try {
            $this->baremeService->createBareme(
                (int) $this->request->getPost('id_type_transaction'),
                (int) $this->request->getPost('montant_min'),
                (int) $this->request->getPost('montant_max'),
                (float) $this->request->getPost('frais')
            );

            return redirect()
                ->to('/operateur/baremes')
                ->with('success', 'Barème ajouté avec succès.');
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function operateurBaremesUpdate($id)
    {
        try {
            $result = $this->baremeService->updateBareme(
                $id,
                (int) $this->request->getPost('montant_min'),
                (int) $this->request->getPost('montant_max'),
                (float) $this->request->getPost('frais')
            );
            if ($result) {
                return redirect()
                    ->to('/operateur/baremes')
                    ->with('success', 'Barème modifié avec succès');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Erreur lors de la modification');
            }
        } catch (Exception $e) {
            //throw $th;
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
