<?php

namespace app\models\Categorie;
use \Illuminate\Database\Eloquent\Model;


class Categorie extends Model {
    protected $table = 'categorie';
    protected $primaryKey = 'id_categorie';
    public $timestamps = false;
}

?>