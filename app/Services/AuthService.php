<?php

namespace App\Services;

use App\Models\UtilisateurModel;

class AuthService
{
    protected UtilisateurModel $userModel;

    public function __construct(){
        $this->userModel = new UtilisateurModel();
    }


    public function login(string $numero, string $codeSecret): ?array
    {
        $user = $this->userModel->findByNumero($numero);

        if (!$user) 
            return null;

        if ($user['code_secret'] !== $codeSecret)
            return null;

        return $user;
    }

    public function logout(){
        session()->destroy();
    }
}