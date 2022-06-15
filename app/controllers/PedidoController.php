<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

require_once './interfaces/IApiUsable.php';
require_once './models/Pedido.php';
require_once './models/ProductoPedido.php';

use \App\Models\Pedido as Pedido;
use \App\Models\ProductoPedido as ProductoPedido;

class PedidoController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo = $parametros['codigo'];
    $estado = $parametros['estado'];
    $mesa = $parametros['mesa'];

    $pedido = new Pedido();
    $pedido->codigo = $codigo;
    $pedido->estado = $estado;
    $pedido->mesa = $mesa;

    if(self::ValidarEstado($estado)){
      $pedido->save();
      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
    }
    else{
      $payload = json_encode(array("ERROR" => "Estado invalido"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $codigoPedido = $args['pedido'];

    $pedido = Pedido::where('codigo',$codigoPedido)->first();

    $productosPedidos = $pedido->productosPedidos;
    foreach($productosPedidos as $pp){
      $pp->producto;
    }

    $payload = json_encode($pedido);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::all();
    foreach($lista as $pedido){
       $productosPedidos = $pedido->productosPedidos;
        foreach($productosPedidos as $pp){
          $pp->producto;
        }
    } 
    $payload = json_encode(array("listaPedidos" => $lista));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigoModificado = $parametros['codigo'];
    $mesaModificada = $parametros['mesa'];
    $precioTotalModificado = $parametros['precioTotal'];
    $estadoModificado = $parametros['estado'];
    $productosModificado = $parametros['productos'];

    $pedidoId = $args['id'];

    $pedido = Pedido::where('id', '=', $pedidoId)->first();
    
    if ($pedido !== null) {
      if(ValidarPerfil($perfilModificado)){
        $pedido = new Pedido();
        $pedido->mesa = $mesaModificada;
        $pedido->codigo = $codigoModificado;
        $pedido->precioTotal = $precioTotalModificado;
        $pedido->estado = $estadoModificado;
        $pedido->productos = $productosModificado;

        $pedido->save();
    
        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
      }
      else{
        $payload = json_encode(array("ERROR" => "Estado invalido"));
      }    
    }else{
      $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $pedidoId = $args['id'];
    $pedido = Pedido::find($pedidoId);
    $pedido->delete();

    $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  private static function ValidarEstado($value){
    $estado = strtoupper($value);
    if($estado=="PENDIENTE" || $estado=="EN PREPARACION" || $estado=="LISTO PARA SERVIR"){
      return true;
    }
    return false;
  }

}