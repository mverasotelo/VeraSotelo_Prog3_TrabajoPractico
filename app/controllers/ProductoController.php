<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

require_once './models/Usuario.php';
require_once './models/Producto.php';
require_once './models/ProductoPedido.php';
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

    if(self::ValidarTipo($tipo)){
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

  public function TraerPendientesCocina($request, $response, $args)
  {
    $lista = self::consultaPendientes("COMIDA")->concat(self::consultaPendientes("POSTRE"));

    $payload = json_encode(array("pendientesCocina" => $lista));  

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPendientesCerveceria($request, $response, $args)
  {
    $payload = json_encode(array("pendientesCerveceria" => self::consultaPendientes("CERVEZA")));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPendientesBarra($request, $response, $args)
  {
    $payload = json_encode(array("pendientesBarra" => self::consultaPendientes("BEBIDA")));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function PrepararCerveceria($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    // $nombreUsuario= $parametros['usuario'];
    $tiempoEstimado = $parametros['tiempoEstimado'];
    $productoPedidoId = $parametros['id'];

    // $usuario=Usuario::where('nombre',$nombreUsuario)->first();
    $producto = ProductoPedido::find($productoPedidoId);
    
    if ($producto !== null && $tiempoEstimado>0){
        if(self::ConsultaTipoProducto($producto) == "CERVEZA"){
          // $producto->usuario_id = $usuario->id;
          $producto->tiempoEstimado = $tiempoEstimado;
          $producto->estado = "EN PREPARACION";
          $producto->save();
          $payload = json_encode(array("mensaje" => "El pedido ".$productoPedidoId." se encuentra en preparacion. Tiempo estimado: ".$producto->tiempoEstimado." minutos."));
        }else{
          $payload = json_encode(array("mensaje" => "El pedido ".$productoPedidoId." no es del sector CERVECERIA"));
        }
      }else{
      $payload = json_encode(array("mensaje" => "Ha ocurrido un error al realizar la operación"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function PrepararCocina($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    // $nombreUsuario= $parametros['usuario'];
    $tiempoEstimado = $parametros['tiempoEstimado'];
    $productoPedidoId = $parametros['id'];

    // $usuario=Usuario::where('nombre',$nombreUsuario)->first();
    $producto = ProductoPedido::find($productoPedidoId);
    
    if ($producto !== null && $tiempoEstimado>0){
        if(self::ConsultaTipoProducto($producto) == "COMIDA" || self::ConsultaTipoProducto($producto) == "POSTRE"){
          // $producto->usuario_id = $usuario->id;
          $producto->tiempoEstimado = $tiempoEstimado;
          $producto->estado = "EN PREPARACION";
          $producto->save();
          $payload = json_encode(array("mensaje" => "El pedido ".$productoPedidoId." se encuentra en preparacion. Tiempo estimado: ".$producto->tiempoEstimado." minutos."));
        }else{
          $payload = json_encode(array("mensaje" => "El pedido ".$productoPedidoId." no es del sector COCINA"));
        }
      }else{
      $payload = json_encode(array("mensaje" => "Ha ocurrido un error al realizar la operación"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function PrepararBarra($request, $response, $args){
    $parametros = $request->getParsedBody();

    // $nombreUsuario= $parametros['usuario'];
    $tiempoEstimado = $parametros['tiempoEstimado'];
    $productoPedidoId = $parametros['id'];

    // $usuario=Usuario::where('nombre',$nombreUsuario)->first();
    $producto = ProductoPedido::find($productoPedidoId);
    
    if ($producto !== null && $tiempoEstimado>0){
        if(self::ConsultaTipoProducto($producto) == "BEBIDA"){
          // $producto->usuario_id = $usuario->id;
          $producto->tiempoEstimado = $tiempoEstimado;
          $producto->estado = "EN PREPARACION";
          $producto->save();
          $payload = json_encode(array("mensaje" => "El pedido ".$productoPedidoId." se encuentra en preparacion. Tiempo estimado: ".$producto->tiempoEstimado." minutos."));
        }else{
          $payload = json_encode(array("mensaje" => "El pedido ".$productoPedidoId." no es del sector BARRA"));
        }
      }else{
      $payload = json_encode(array("mensaje" => "Ha ocurrido un error al realizar la operación"));
    }
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
    private static function ValidarTipo($value){
      $tipo = strtoupper($value);
      if($tipo=="COMIDA" || $tipo=="BEBIDA" || $tipo=="CERVEZA" || $tipo=="POSTRE"){
          return true;
      }
      return false;
    }

    private function ConsultaPendientes($tipo){
      return ProductoPedido::select('pedido_producto.pedido_id','pedido_producto.cantidad','productos.nombre','productos.tipo')
      ->join('productos', 'pedido_producto.producto_id', '=', 'productos.id')
      ->where('productos.tipo',$tipo)->where('pedido_producto.estado','PENDIENTE')->get();
    }

    private function ConsultaTipoProducto($productoPedido){
      return ProductoPedido::select('productos.tipo')
      ->join('productos', $productoPedido->producto_id, '=', 'productos.id');
    }
}
