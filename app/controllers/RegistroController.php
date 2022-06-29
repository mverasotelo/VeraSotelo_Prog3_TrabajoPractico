<?php

require_once './interfaces/IApiUsable.php';
require_once './models/RegistroEmpleados.php';

use \App\Models\RegistroEmpleados as RegistroEmpleados;

class RegistroController
{
  public function TraerTodos($request, $response, $args)
  {
    $lista = RegistroEmpleados::all();

    if(count($lista)){
        $payload = json_encode(array("Registros" => $lista));
    }else{
        $payload = json_encode(array("Mensaje" => "No hay registros"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerRegistrosPorEmpleado($request, $response, $args)
  {
    $idEmpleado = intval($args['idEmpleado']);
    $lista = RegistroEmpleados::select('registros_empleados.empleado_id as ID Empleado','usuarios.nombre as Nombre Empleado', 'registros_empleados.operacion as Operacion', 'registros_empleados.created_at as Fecha')
    ->join('usuarios', 'usuarios.id','=', 'registros_empleados.empleado_id')->where('empleado_id',$idEmpleado)->get();

    if(count($lista)>0){
        $payload = json_encode(array("Registros" => $lista));
    }else{
        $payload = json_encode(array("Mensaje" => "No hay registros para el empleado con ID ".$idEmpleado));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
    
}