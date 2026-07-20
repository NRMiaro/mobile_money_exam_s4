<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }
        $routePath = $request->getUri()->getPath();
        if ($routePath === '/' || $routePath === ''){
            if (session()->get('isAdmin') == true) {
                return redirect()->to('/operateur');
            }
            
            return redirect()->to('/client/dashboard');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}