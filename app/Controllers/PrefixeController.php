<?php

namespace App\Controllers;

use App\Services\OperateurService;
use App\Services\PrefixeService;

class PrefixeController extends BaseController
{
    protected PrefixeService $service;
    protected OperateurService $operateurService;

    public function __construct()
    {
        $this->service = new PrefixeService();
        $this->operateurService = new OperateurService();
    }

    public function index(): string
    {
        $data = $this->service->getPrefixesParOperateur();

        return view('operateur/prefixe/index', $data);
    }

    public function create(): string
    {
        return view('operateur/prefixe/create', [
            'operateurs' => $this->operateurService->getAll()
        ]);
    }

    public function store()
    {
        $data = [
            'prefixe' => $this->request->getPost('prefixe'),
            'id_operateur' => (int) $this->request->getPost('id_operateur'),
        ];

        // dd($data);
        // return; 

        $result = $this->service->create($data);

        if ($result === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $this->service->getErrors()));
        }

        return redirect()->to('/operateur/prefixes')
            ->with('success', 'Préfixe ajouté avec succès.');
    }
}
