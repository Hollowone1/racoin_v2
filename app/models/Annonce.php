<?php

namespace app\models\Annonce;

use \Illuminate\Database\Eloquent\Model;

class Annonce extends Model {
    protected $table = 'annonce';
    protected $primaryKey = 'id_annonce';
    public $timestamps = false;
    public $links = null;


    public function annonceur()
    {
        return $this->belongsTo('model\Annonceur', 'id_annonceur');
    }

    public function photo()
    {
        return $this->hasMany('model\Photo', 'id_photo');
    }

}
?>
