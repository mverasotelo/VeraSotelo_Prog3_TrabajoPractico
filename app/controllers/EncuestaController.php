<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

require_once './models/Encuesta.php';
require_once './models/Mesa.php';
require_once './models/Pedido.php';

use \App\Models\Encuesta as Encuesta;
use \App\Models\Mesa as Mesa;
use \App\Models\Pedido as Pedido;

class EncuestaController
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigoMesa = $parametros['puntuacionMesa'];
    $codigoPedido = $parametros['codigoPedido'];
    $puntuacionMesa = $parametros['puntuacionMesa'];
    $puntuacionMozo = $parametros['puntuacionMozo'];
    $puntuacionRestaurante = $parametros['puntuacionRestaurante'];
    $puntuacionCocinero = $parametros['puntuacionCocinero'];
    $comentario = $parametros['comentario'];

      if(!empty(Mesa::where('codigo', $codigoMesa)->get())){
        if(!empty(Pedido::where('codigo', $codigoPedido)->get())){
          if(Self::chequearPuntajeValido($puntuacionMesa) && Self::chequearPuntajeValido($puntuacionMozo) 
          && Self::chequearPuntajeValido($puntuacionMesa) && Self::chequearPuntajeValido($puntuacionMesa))
          {
          $encuesta = new Encuesta();
          $encuesta->codigoMesa = $codigoMesa;
          $encuesta->codigoPedido = $codigoPedido;
          $encuesta->puntuacionMesa = $puntuacionMesa;
          $encuesta->puntuacionMozo = $puntuacionMozo;
          $encuesta->puntuacionRestaurante = $puntuacionRestaurante;
          $encuesta->puntuacionCocinero = $puntuacionCocinero;
          $encuesta->comentario = $comentario;
          $encuesta->save();
    
          $payload = json_encode(array("mensaje" => "Encuesta guardada con exito con ID ".$encuesta->id));  

        }else{
          $payload = json_encode(array("mensaje" => "No es un puntaje valido"));
        }
      }else{
        $payload = json_encode(array("ERROR" => "El codigo de pedido ingresado no existe"));
      }
    }else{
      $payload = json_encode(array("ERROR" => "El codigo de mesa ingresado no existe"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $id = $args['id'];

    $encuesta = Encuesta::find($id);

    if($encuesta){
        $payload = json_encode($encuesta);
    }else{
        $payload = json_encode(array("ERROR" => "No existe una encuesta con ese id"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Encuesta::all();

    if(count($lista)){
      $payload = json_encode(array("listaEncuestas" => $lista));
    }else{
      $payload = json_encode(array("mensaje" => "No existen encuestas"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  private static function chequearPuntajeValido($puntaje){
    if($puntaje>0 && $puntaje<=10){
      return true;
    }else{
      return false;
    }
  }
    
}