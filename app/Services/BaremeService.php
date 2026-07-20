<?php

namespace App\Services;

use App\Models\BaremeModel;
use App\Models\TypeTransactionModel;

class BaremeService
{

    private BaremeModel $model;

    public function __construct()
    {
        $this->model = new BaremeModel();
    }

    public function getBaremes($idTypetransaction)
    {
        return $this->model
            ->where('id_type_transaction', $idTypetransaction)
            ->orderBy('montant_min', 'ASC')
            ->findAll();
    }

    public function getBaremesDepot()
    {
        return $this->getBaremes(TypeTransactionModel::DEPOT_ID);
    }

    public function getBaremesRetrait()
    {
        return $this->getBaremes(TypeTransactionModel::RETRAIT_ID);
    }

    public function getBaremesTransfert()
    {
        return $this->getBaremes(TypeTransactionModel::TRANSFERT_ID);
    }

    public function getContexteModification(int $id): array
    {
        $current = $this->model->find($id);

        $avant = $this->model
            ->where('id_type_transaction', $current['id_type_transaction'])
            ->where('montant_min <', $current['montant_min'])
            ->orderBy('montant_min', 'DESC')
            ->first();


        $apres = $this->model
            ->where('id_type_transaction', $current['id_type_transaction'])
            ->where('montant_min >', $current['montant_min'])
            ->orderBy('montant_min', 'ASC')
            ->first();


        return [
            'avant' => $avant,
            'courant' => $current,
            'apres' => $apres
        ];
    }

    public function updateBareme(
        int $id,
        int $montantMin,
        int $montantMax,
        float $frais
    ): bool {

        $db = \Config\Database::connect();

        $db->transStart();

        // Barème actuel
        $current = $this->model->find($id);

        if ($montantMax <= $montantMin) {
            throw new \Exception(
                "Le montant maximum doit être supérieur au minimum"
            );
        }

        // Chercher le précédent
        $precedent = $this->model
            ->where('id_type_transaction', $current['id_type_transaction'])
            ->where('montant_min <', $current['montant_min'])
            ->orderBy('montant_min', 'DESC')
            ->first();


        // Chercher le suivant
        $suivant = $this->model
            ->where('id_type_transaction', $current['id_type_transaction'])
            ->where('montant_min >', $current['montant_min'])
            ->orderBy('montant_min', 'ASC')
            ->first();



        /*
        * Mise à jour du voisin précédent
        */
        if ($precedent) {
            $this->model->update(
                $precedent['id'],
                [
                    'montant_max' => $montantMin - 1
                ]
            );
        }



        /*
        * Mise à jour du voisin suivant
        */
        if ($suivant) {
            $this->model->update(
                $suivant['id'],
                [
                    'montant_min' => $montantMax + 1
                ]
            );
        }



        /*
        * Mise à jour de la ligne courante
        */
        $this->model->update(
            $id,
            [
                'montant_min' => $montantMin,
                'montant_max' => $montantMax,
                'frais' => $frais
            ]
        );


        $db->transComplete();
        return $db->transStatus();
    }
}
