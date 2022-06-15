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

    protected $primaryKey = 'id';
    protected $fillable = ['codigo','tiempoEstimado','precioTotal','mesa'];
    public $incrementing = true;
    public $timestamps = false;

    public function productosPedidos(){
        return $this->hasMany(ProductoPedido::class, 'pedido_id');
    }

    const DELETED_AT = 'fechaBaja';

}
?>