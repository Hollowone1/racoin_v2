<?php

namespace app\models\Annonceur;
use \Illuminate\Database\Eloquent\Model;

class Annonceur extends Model {
    protected $table = 'annonceur';
    protected $primaryKey = 'id_annonceur';
    public $timestamps = false;

    public function annonce()
    {
        return $this->hasMany('model\Annonce', 'id_annonceur');
    }
}
