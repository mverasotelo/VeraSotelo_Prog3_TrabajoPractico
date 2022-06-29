<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoPedido extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'pedido_producto';
    protected $fillable = ['producto_id','pedido_id','cantidad','estado','tiempoEstimado'];
    public $incrementing = true;

    public function producto(){
        return $this->hasOne(Producto::class, 'id');
    }

    public function empleado(){
        return $this->hasOne(Usuario::class, 'id');
    }

    public function pedidos(){
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
 
?>