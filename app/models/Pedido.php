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
    protected $fillable = ['codigo','tiempoEstimado','estado','foto','cliente','precioTotal','mesa_id'];
    public $incrementing = true;

    public function mesa(){
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
    
    public function productosPedidos(){
        return $this->hasMany(ProductoPedido::class, 'pedido_id');
    }

}
?>