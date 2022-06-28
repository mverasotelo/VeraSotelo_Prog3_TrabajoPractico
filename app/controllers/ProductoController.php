<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

require_once './models/Usuario.php';
require_once './models/Producto.php';
require_once './models/ProductoPedido.php';
require_once './services/ManejoArchivos.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Usuario as Usuario;
use \App\Models\Producto as Producto;
use \App\Models\ProductoPedido as ProductoPedido;

class ProductoController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $tipo = $parametros['tipo'];
    $precio = $parametros['precio'];
    $stock = $parametros['stock'];

    if(Self::ValidarTipo($tipo)){
      $producto = new Producto();
      $producto->nombre = $nombre;
      $producto->tipo = $tipo;
      $producto->precio = $precio;
      $producto->stock = $stock;
      $producto->save();
  
      $payload = json_encode(array("mensaje" => "Producto creado con exito"));
    }
    else{
      $payload = json_encode(array("ERROR" => "Tipo invalido"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $id = $args['id'];

    $producto = Producto::find('id');

    $payload = json_encode($producto);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::all();
    $payload = json_encode(array("listaProductos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  
  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nuevoNombre = $parametros['nombre'];
    $nuevoPrecio = $parametros['precio'];
    $nuevoTipo = $parametros['tipo'];
    $nuevoStock = $parametros['stock'];

    $productoId = $args['id'];

    $producto = Producto::where('id', '=', $productoId)->first();
    
    if ($producto !== null) {
      if(ValidarPerfil($nuevoTipo)){
        $producto = new Usuario();
        $producto->nombre = $nuevoNombre;
        $producto->precio = $nuevoPrecio;
        $producto->stock = $nuevoStock;
        $producto->tipo = $nuevoTipo;
        $producto->save();
    
        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
      }
      else{
        $payload = json_encode(array("ERROR" => "Tipo invalido"));
      }    
    }else{
      $payload = json_encode(array("mensaje" => "Producto no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $productoId = $args['id'];
    $producto = Producto::find($usuarioId);
    $producto->delete();

    $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function DescargarCsv($request, $response, $args)
  {
    ManejoArchivos::descargarCsv();
    
    return $response
    ->withHeader('Content-Type', 'Content-Type: text/csv');
  }
  
  public function LeerCsv($request, $response, $args)
  {
    if(ManejoArchivos::leerCsv()){
      $payload = json_encode(array("mensaje" => "Listado cargado con exito"));
    }else{
      $payload = json_encode(array("ERROR" => "Error al cargar el listado"));
    }

    $response->getBody()->write($payload);

    return $response
    ->withHeader('Content-Type', 'application/json');
  }

  private static function ValidarTipo($value){
    $tipo = strtoupper($value);
    if($tipo=="COMIDA" || $tipo=="BEBIDA" || $tipo=="CERVEZA" || $tipo=="POSTRE"){
        return true;
    }
    return false;
  }

}
