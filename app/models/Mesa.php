<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model{

    use SoftDeletes;

    protected $primaryKey = 'codigo';
    protected $fillable = ['codigo','estado','cliente'];
    public $incrementing = true;
    public $timestamps = false;

    public function pedidos(){
        return $this->hasMany(Pedido::class, 'mesa');
    }

    const DELETED_AT = 'fechaBaja';

}
?>