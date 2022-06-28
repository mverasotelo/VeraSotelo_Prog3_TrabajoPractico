<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $fillable = ['nombre','tipo','precio','stock'];
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fechaBaja';

}
?>