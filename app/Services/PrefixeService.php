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

    public function getAll(): array
    {
        return $this->model->orderBy('prefixe', 'ASC')->findAll();
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
}