<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $fillable = ['mesa','mozoId', 'pedidoId', 'comentario','precioTotal'];
    public $incrementing = true;
    public $timestamps = false;

    const CREATED_AT = 'fechaFactura';
    const DELETED_AT = 'fechaBaja';

}
?>