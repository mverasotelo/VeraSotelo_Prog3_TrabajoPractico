<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encuesta extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $fillable = ['codigoPedido', 'codigoMesa','puntuacionMesa','puntuacionMozo','puntuacionRestaurante', 'puntuacionCocinero', 'comentario'];
    public $incrementing = true;
    public $timestamps = false;

    const CREATED_AT = 'fechaEncuesta';
    const DELETED_AT = 'fechaBaja';

}
?>