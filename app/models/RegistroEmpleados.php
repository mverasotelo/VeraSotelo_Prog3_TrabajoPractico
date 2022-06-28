<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistroEmpleados extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'registros_empleados';
    protected $fillable = ['empleado_id','operacion'];
    public $incrementing = true;

    public function empleado(){
        return $this->belongsTo(Usuario::class, 'empleado_id');
    }
}

?>