<?php

namespace app\models\Region;
use \Illuminate\Database\Eloquent\Model;

class Region extends Model {
    protected $table = 'region';
    protected $primaryKey = 'id_region';
    public $timestamps = false;
}

?>