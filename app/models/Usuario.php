<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $fillable = ['nombre','clave','perfil'];
    public $incrementing = true;
    
    public function registros(){
        return $this->hasMany(RegistroEmpleados::class, 'empleado_id');
    }

}

?>