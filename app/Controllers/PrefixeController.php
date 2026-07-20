<?php

namespace App\Controllers;

use App\Services\PrefixeService;

class PrefixeController extends BaseController
{
    protected PrefixeService $service;

    public function __construct()
    {
        $this->service = new PrefixeService();
    }

    public function index(): string
    {
        return view('operateur/prefixe/index', [
            'prefixes' => $this->service->getAll(),
        ]);
    }

    public function create(): string
    {
        return view('operateur/prefixe/create');
    }

    public function store()
    {
        $data = [
            'prefixe' => $this->request->getPost('prefixe'),
        ];

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