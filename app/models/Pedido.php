<?php

/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model{

    use SoftDeletes;

    protected $primaryKey = 'codigo';
    protected $fillable = ['codigo','estado','precioTotal','mesa','productos'];
    public $incrementing = true;
    public $timestamps = false;

    const DELETED_AT = 'fechaBaja';

}
?>