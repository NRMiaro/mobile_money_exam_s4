<?php namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Services\AuthService;

class AuthController extends BaseController {

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin(){
        return view('auth/login');
    }

    public function login(){
        $request = $this->request;
        $data = $request->getPost();
        // suppression d'espaces dans le numero
        $numero = str_replace(' ', '', $data['numero']);
        $codeSecret = $data['code_secret'];
        $user = $this->authService->login($numero, $codeSecret);
        if ($user === null){
            return redirect()
                ->to('login')
                ->with('error', 'Identifiants invalides');
        }

        /* UPDATE session */
        session()->set('isLoggedIn', true);
        session()->set('isAdmin', $user['is_admin']);
        session()->set('idUtilisateur', $user['id']);
        echo session()->get('idUtilisateur');

        if (session()->get('isAdmin') == 1)
            return redirect()->to('/operateur');
        else 
            return redirect()->to('/client');
    }

    public function logout(){
        $this->authService->logout();
        return redirect()->to('/');
    }
}