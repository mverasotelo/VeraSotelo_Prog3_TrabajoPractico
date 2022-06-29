<?php

require_once './interfaces/IApiUsable.php';
require_once './models/Mesa.php';

use \App\Models\Mesa as Mesa;

class MesaController implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo = $parametros['codigo'];

    $mesa = new Mesa();
    $mesa->codigo = $codigo;
    $mesa->save();

    $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $codigo = $args['codigo'];

    $mesa = Mesa::where('codigo',$codigo)->first();
    if($mesa->pedidos!=null){
      $mesa->pedidos;
    }

    $payload = json_encode(array("listaMesas" => $mesa));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Mesa::all();
    foreach($lista as $mesa){
      $mesa->pedidos;
    }

    $payload = json_encode(array("listaMesas" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nuevoEstado = $parametros['estado'];
    $mesaCodigo = $args['codigo'];

    // Conseguimos el objeto
    $mesa = Mesa::where('codigo', $mesaCodigo)->first();

    // Si existe
    if ($mesa !== null) {
      // Seteamos una nueva mesa 
      $mesa->estado = $nuevoEstado;
      $mesa->save();

      $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $mesaId = $args['id'];

    $mesa = Mesa::find($mesaId);

    $mesa->delete();

    $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function cambiarAMesaServida($request, $response, $args)
  {
    $mesaCodigo = $args['codigo'];

    $mesa = Mesa::where('codigo', $mesaCodigo)->first();

    if ($mesa !== null) {
      if($mesa->estado != "CON CLIENTES COMIENDO"){
        $mesa->estado = "CON CLIENTES COMIENDO";
        $mesa->save();
        $payload = json_encode(array("mensaje" => "Mesa ".$mesaCodigo." con clientes comiendo"));
      }else{
        $payload = json_encode(array("mensaje" => "La mesa ".$mesaCodigo." ya se encontraba con clientes comiendo"));

      }
    } else {
      $payload = json_encode(array("mensaje" => "Mesa ".$mesaCodigo." no encontrada"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CerrarMesa($request, $response, $args)
  {
    $mesaCodigo = $args['codigo'];

    $mesa = Mesa::where('codigo', $mesaCodigo)->first();

    if ($mesa !== null) {
      if($mesa->estado != "CERRADA"){
        $mesa->estado = "CERRADA";
        $mesa->save();
        $payload = json_encode(array("mensaje" => "Mesa ".$mesaCodigo." cerrada con exito"));
      }else{
        $payload = json_encode(array("mensaje" => "La mesa ".$mesaCodigo." ya se encontraba cerrada"));

      }
    } else {
      $payload = json_encode(array("mensaje" => "Mesa ".$mesaCodigo." no encontrada"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerMesaMasUsada($request, $response, $args)
  {

    $mesa = Pedido::groupBy('mesa_id')->get();
    $payload = json_encode(array("Mesa mas usada" => $mesa));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  private static function ValidarEstado($value){
    $estado = strtoupper($value);
    if($estado=="CON CLIENTES ESPERANDO PEDIDO" || $estado=="CON CLIENTES COMIENDO" ||  $estado=="CON CLIENTES PAGANDO" || $estado=="CERRADA"){
      return true;
    }
    return false;
  }

}