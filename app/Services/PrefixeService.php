<?php

namespace App\Services;

use App\Models\PrefixeModel;

class PrefixeService
{
    protected PrefixeModel $model;

    public function __construct()
    {
        $this->model = new PrefixeModel();
    }

    public function getAll(bool $onlyOwner = false): array
    {
        if ($onlyOwner) {
            $this->model->where('id_operateur', 1);
        }
        return $this->model->orderBy('prefixe', 'ASC')->findAll();
    }

    public function getPrefixesParOperateur(): array
    {
        $owner = $this->model
            ->where('id_operateur', 1)
            ->orderBy('prefixe', 'ASC')
            ->findAll();

        $others = $this->model
            ->select('prefixe.*, operateur.libelle AS operateur')
            ->join('operateur', 'operateur.id = prefixe.id_operateur')
            ->where('id_operateur !=', 1)
            ->orderBy('operateur.libelle')
            ->orderBy('prefixe')
            ->findAll();

        $grouped = [];

        foreach ($others as $prefixe) {
            $grouped[$prefixe['operateur']][] = $prefixe;
        }

        return [
            'owner'  => $owner,
            'others' => $grouped
        ];
    }

    public function getById(int $id): ?array
    {
        return $this->model->find($id);
    }

    public function getTotalPrefixes()
    {
        return $this->model->countAllResults();
    }

    public function create(array $data): bool|int
    {
        $data['prefixe'] = trim($data['prefixe'] ?? '');

        if (! $this->model->insert($data)) {
            return false;
        }

        return $this->model->getInsertID();
    }

    public function isPrefixeValide(string $numero): bool
    {
        $numero = trim($numero);
        foreach ($this->getAll() as $p) {
            if (str_starts_with($numero, $p['prefixe'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Renvoie les erreurs de validation du modèle (déclenchées par validationRules)
     */
    public function getErrors(): array
    {
        return $this->model->errors() ?? [];
    }

    public function getOperateurByNumero(string $numero): ?int
    {
        $numero = trim($numero);

        $prefixe = substr($numero, 0, 3);

        $result = $this->model
            ->where('prefixe', $prefixe)
            ->first();

        if (!$result) {
            return null;
        }

        return (int) $result['id_operateur'];
    }
}
