<?php 
namespace App\Models ;

use CodeIgniter\Model;

class PromotionModel extends Model{
    protected $table = 'promotion';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'pourcentage',
        'is_actif',
    ];

    protected  array $casts = [
        'id' => 'int',
    ];

    public function getPromoActive(){
        return $this->where('is_actif',1)
                    ->orderBy('id','DESC')
                    ->first();
    }


}