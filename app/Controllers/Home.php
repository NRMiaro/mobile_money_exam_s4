<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        try {
            $db = db_connect();
            $db->query('SELECT 1');

            echo "SQLite database connected successfully!";
        } catch (\Throwable $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
