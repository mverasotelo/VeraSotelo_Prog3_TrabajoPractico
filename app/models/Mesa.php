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

    const DELETED_AT = 'fechaBaja';

    
    // //Atributos
    // public $codigo;
    // public $estado;
    // public $cliente;

    // public function __construct($codigo=null,$estado=null,$cliente=null){
    //     $this->setCodigo($codigo);
    //     $this->setEstado($estado);
    //     $this->cliente = $cliente;
    // }

    // /**
    //  * Valida y establece el estado del pedido
    //  *
    //  * @param string $codigo
    //  */
    // public function setCodigo($codigo){
    //     if ($codigo!= null && is_string($codigo) && strlen($codigo)==5){
    //             $this->codigo = $codigo;
    //     }
    // }

    // /**
    //  * Valida y establece el estado de la mesa
    //  *
    //  * @param string $estado
    //  */
    // public function setEstado($value){
    //     if ($value!= null && is_string($value)){
    //         $estado = strtolower($value);
    //         if($estado=="con cliente esperando pedido" || $estado=="con cliente comiendo" || $estado=="con cliente pagando" || $estado=="cerrada"){
    //             $this->estado = $estado;
    //         }
    //     }
    // }

    // public function crearMesa()
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo, estado, cliente) VALUES (:codigo, :estado, :cliente)");
    //     $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
    //     $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
    //     $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
    //     $consulta->execute();

    //     return $objAccesoDatos->obtenerUltimoId();
    // }

    // public static function obtenerTodos()
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo, estado, cliente FROM mesas");
    //     $consulta->execute();

    //     return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    // }
}
?>