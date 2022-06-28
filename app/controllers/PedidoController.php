<?php
/*
Mercedes Vera Sotelo
Trabajo Práctico
*/

require_once './interfaces/IApiUsable.php';
require_once './models/Pedido.php';
require_once './models/Mesa.php';
require_once './models/Producto.php';
require_once './models/ProductoPedido.php';
require_once './services/ManejoArchivos.php';

use \App\Models\Pedido as Pedido;
use \App\Models\Mesa as Mesa;
use \App\Models\ProductoPedido as ProductoPedido;
use \App\Models\Producto as Producto;

class PedidoController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo = $parametros['codigo'];
    $mesa_codigo = $parametros['mesa'];
    $cliente = $parametros['cliente'];
    $uploadedFiles = $request->getUploadedFiles();
    $archivo = $uploadedFiles['foto'];

    $mesa = Mesa::where('codigo', '=', $mesa_codigo)->first();
    if($mesa){
        $pedido = new Pedido();
        $pedido->mesa_id = $mesa->id;
        $pedido->codigo = $codigo;
        $pedido->estado = "CON CLIENTE ESPERANDO PEDIDO";
        $pedido->cliente = $cliente;
        $pedido->save();
        $foto = ManejoArchivos::guardarImagenClientes($pedido,$archivo);
        if($foto!=null){
          $pedido->foto = $foto;
          $pedido->save();  
          $payload = json_encode(array("MENSAJE" => "Pedido realizado con exito"));  
        }else{
          $payload = json_encode(array("ERROR" => "Ha ocurrido un error al guardar la foto de la mesa"));  
        }
    }else{
      $payload = json_encode(array("ERROR" => "El codigo de la mesa no existe"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $codigoPedido = $args['codigoPedido'];

    $pedido = Pedido::select('pedidos.codigo','pedidos.tiempoEstimado')->where('codigo',$codigoPedido)->first();

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

    $pedidoId = $args['id'];
    $parametros = $request->getParsedBody();

    $codigoModificado = $parametros['codigo'];
    $mesaModificada = $parametros['mesa'];
    $precioTotalModificado = $parametros['precioTotal'];
    $estadoModificado = $parametros['estado'];
    $productosModificado = $parametros['productos'];

    $pedido = Pedido::where('id', '=', $pedidoId)->first();
    
    if($mesa = Mesa::where('codigo', '=', $mesaModificada)->first()){
      if ($pedido !== null) {
        if(ValidarPerfil($perfilModificado)){
          $pedido = new Pedido();
          $pedido->codigo = $codigoModificado;
          $pedido->mesa_id = $mesa->id;
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

    $payload = json_encode(array("MENSAJE" => "Pedido borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function AgregarProducto($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigoPedido = $parametros['codigoPedido'];
    $productoId = $parametros['productoId'];
    $cantidad = $parametros['cantidad'];

    if($codigoPedido && $codigoPedido && $codigoPedido){
      $pedido = Pedido::where('codigo','=',$codigoPedido)->first();
      if($pedido){
        if(Producto::find($productoId)){
          $producto = new ProductoPedido();
          $producto->pedido_id=$pedido->id;
          $producto->producto_id=$productoId;
          $producto->cantidad=$cantidad;
          $producto->estado="PENDIENTE";
          $producto->save();
          $payload = json_encode(array("MENSAJE" => "Producto agregado al pedido ".$codigoPedido));
        }else{
          $payload = json_encode(array("ERROR" => "El codigo del producto no existe"));
        }
      }else{
        $payload = json_encode(array("ERROR" => "El codigo de pedido no existe"));
      }
    }else{
      $payload = json_encode(array("ERROR" => "Faltan cargar campos"));
    }


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

  
  public function TraerPendientesCocina($request, $response, $args)
  {
    $lista = Self::consultaPendientes("COMIDA")->concat(self::consultaPendientes("POSTRE"));

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
    $payload = json_encode(array("pendientesBarra" => Self::consultaPendientes("BEBIDA")));

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
    
    if ($producto && $tiempoEstimado>0){
      if(Self::ConsultaTipoProducto($producto) == "CERVEZA"){
        // $producto->usuario_id = $usuario->id;
        $producto->tiempoEstimado = $tiempoEstimado;
        $producto->estado = "EN PREPARACION";
        $producto->save();
        Self::actualizarTiempoEstimado($producto->pedido_id, $producto->tiempoEstimado);
        $payload = json_encode(array("MENSAJE" => "El pedido ".$productoPedidoId." se encuentra en preparacion. Tiempo estimado: ".$producto->tiempoEstimado." minutos."));
      }else{
        $payload = json_encode(array("ERROR" => "El pedido ".$productoPedidoId." no es del sector CERVECERIA"));
      }
    }else{
      $payload = json_encode(array("ERROR" => "Ha ocurrido un error al realizar la operación"));
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

    if ($producto && $tiempoEstimado>0){
        if(Self::ConsultaTipoProducto($producto) == "COMIDA" || Self::ConsultaTipoProducto($producto) == "POSTRE"){
          // $producto->usuario_id = $usuario->id;
          $producto->tiempoEstimado = $tiempoEstimado;
          $producto->estado = "EN PREPARACION";
          $producto->save();
          Self::actualizarTiempoEstimado($producto->pedido_id,$producto->tiempoEstimado);
          $payload = json_encode(array("MENSAJE" => "El pedido ".$productoPedidoId." se encuentra en preparacion. Tiempo estimado: ".$producto->tiempoEstimado." minutos."));
        }else{
          $payload = json_encode(array("ERROR" => "El pedido ".$productoPedidoId." no es del sector COCINA"));
        }
      }else{
      $payload = json_encode(array("ERROR" => "Ha ocurrido un error al realizar la operación"));
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
    
    if ($producto  && $tiempoEstimado>0){
        if(self::ConsultaTipoProducto($producto) == "BEBIDA"){
          // $producto->usuario_id = $usuario->id;
          $producto->tiempoEstimado = $tiempoEstimado;
          $producto->estado = "EN PREPARACION";
          $producto->save();
          Self::actualizarTiempoEstimado($producto->pedido_id, $$producto->tiempoEstimado);
          $payload = json_encode(array("MENSAJE" => "El pedido ".$productoPedidoId." se encuentra en preparacion. Tiempo estimado: ".$producto->tiempoEstimado." minutos."));
        }else{
          $payload = json_encode(array("ERROR" => "El pedido ".$productoPedidoId." no es del sector BARRA"));
        }
      }else{
      $payload = json_encode(array("ERROR" => "Ha ocurrido un error al realizar la operación"));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  private function ConsultaPendientes($tipo){
    return ProductoPedido::select('pedido_producto.id','pedido_producto.pedido_id','pedido_producto.cantidad','productos.nombre','productos.tipo')
    ->join('productos', 'pedido_producto.producto_id', '=', 'productos.id')
    ->where('productos.tipo',$tipo)->where('pedido_producto.estado','PENDIENTE')->get();
  }

  private function ConsultaTipoProducto($productoPedido){
    $producto = ProductoPedido::select('productos.tipo')->join('productos', 'productos.id', '=','pedido_producto.producto_id')
    ->where('productos.id', '=',$productoPedido->producto_id)->first();
    return $producto->tipo;
  }

  private function actualizarTiempoEstimado($idPedido, $tiempoEstimado){
    $pedido = Pedido::find($idPedido);
    if($pedido->tiempoEstimado < $tiempoEstimado){
      $pedido->tiempoEstimado = $tiempoEstimado;
    }
  }

}