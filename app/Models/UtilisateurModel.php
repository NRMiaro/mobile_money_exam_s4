<?php

namespace App\Models;

use CodeIgniter\Model;

class UtilisateurModel extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $allowedFields = [
        'nom',
        'prenom',
        'numero',
        'date_naissance',
        'code_secret',
        'solde',
        'is_actif',
        'is_admin'
    ];

    protected array $casts = [
        'id' => 'int',
        'solde' => 'float',
        'is_actif' => 'boolean',
        'is_admin' => 'boolean',
    ];

    protected $validationRules = [
        'nom' => 'required',
        'prenom' => 'required',
        'numero' => 'required|is_unique[utilisateur.numero]',
        'date_naissance' => 'required',
        'code_secret' => 'required',
    ];

    protected $beforeInsert = ['hashCodeSecret'];

    public function hashCodeSecret(array $data)
    {
        // if (isset($data['data']['code_secret'])) {
        //     $data['data']['code_secret'] = password_hash(
        //         $data['data']['code_secret'],
        //         PASSWORD_DEFAULT
        //     );
        // }

        return $data;
    }
}