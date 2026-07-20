<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter');
        }
        
        if (!session()->get('isAdmin')) {
            return redirect()->to('/client')->with('error', 'Acces reserve a l\'operateur');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}